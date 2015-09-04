---
layout:     post
title:      Render XAML to image, Blur app UI, or how to enable awesome user experiences in your UWP app 
date:       2015-09-04 12:31:19
summary:    Saving XAML Visual Tree to an image allows for some fun scenarios, especially when paired with libraries such as Win2D. Frosted Glass is just one basic example of what is possible
permalink: /render-xaml-to-image-and-more/
---

![Frosted Glass Effect](http://i.imgur.com/48GXe09.gif)

It feels like everyone around me is writing UWP apps now, and talking about it. I probably should too... write an app I mean. [I got the talking covered](https://channel9.msdn.com/Shows/This+Week+On+Channel+9). If you are working on something cool, [I'd love to hear about it.](http://twitter.com/metulev)

Anyway, during a very intense discussion about visual effects and shaders, the discussion naturally evolved towards how to use Win2D to improve the UX of an app, and whether it is possible to, for example, blur the whole UI. Half hour later, I was back at my dev machine with a working solution. The solution is so simple, it's a crime not to share it. So read on if interested.

## RenderTargetBitmap ##

### using Windows.UI.Xaml.Media.Imaging; ###

[RenderTargetBitmap](https://msdn.microsoft.com/en-us/library/windows/apps/windows.ui.xaml.media.imaging.rendertargetbitmap) has been available since Windows 8.1 and now on the Universal Windows Platform. For those that haven't used it before, it is an amazing API that enables you to save a XAML Visual Tree of an element as image data. Since RenderTargetBitmap inherits from ImageSource, it can be directly applied to the Source property of a XAML image.

``` csharp
RenderTargetBitmap rtb = new RenderTargetBitmap(); 
await renderTargetBitmap.RenderAsync(uielement); 
image.Source = rtb; 
```

Easy.

To make this easier to use with other APIs (as seen below), I recommend created an extension method that exposes the image data as a stream. Here is my implementation:

``` csharp
public static async Task<IRandomAccessStream> RenderToRandomAccessStream(this UIElement element)
{
    RenderTargetBitmap rtb = new RenderTargetBitmap();
    await rtb.RenderAsync(element);

    var pixelBuffer = await rtb.GetPixelsAsync();
    var pixels = pixelBuffer.ToArray();

    // Useful for rendering in the correct DPI
    var displayInformation = DisplayInformation.GetForCurrentView();

    var stream = new InMemoryRandomAccessStream();
    var encoder = await BitmapEncoder.CreateAsync(BitmapEncoder.PngEncoderId, stream);
    encoder.SetPixelData(BitmapPixelFormat.Bgra8,
                         BitmapAlphaMode.Premultiplied,
                         (uint)rtb.PixelWidth,
                         (uint)rtb.PixelHeight,
                         displayInformation.RawDpiX,
                         displayInformation.RawDpiY,
                         pixels);

    await encoder.FlushAsync();
    stream.Seek(0);

    return stream;
}
```

** Now, before going crazy with this, make sure to read through the [Remarks](https://msdn.microsoft.com/en-us/library/windows/apps/windows.ui.xaml.media.imaging.rendertargetbitmap#xaml_visuals_and_rendertargetbitmap_capture_capabilities). There are some restriction around what can be rendered that you should know about, but luckily, the large majority of cases will work just fine. 

## Win2D ##
 
### using Microsoft.Graphics.Canvas; ###

There is a lot of things I can say about Win2D, but I think it's best if you just checkout the [GitHub](https://github.com/Microsoft/Win2D) page and get enlightened by the beauty that is *Windows Runtime API for immediate mode 2D graphics rendering with GPU acceleration*. Paring image data and Win2D yields pure magic. Don't believe me, read [this](http://blogs.msdn.com/b/eternalcoding/archive/2015/06/23/using-win2d-to-apply-effects-on-your-files.aspx) by [David Catuhe](https://twitter.com/deltakosh), the creator of [babylonJS](http://www.babylonjs.com/) (among other things).

One of the coolest things about Win2D, in my opinion, are the super easy to use and very powerful hardware accelerated effects that don't require intimate knowledge of your graphics card. For example, the GaussianBlurEffect can be used to create a very cool blur effect that can give us that Frosted Glass look in our app. 

``` csharp
using (var stream = await Content.RenderToRandomAccessStream())
{
    var device = new CanvasDevice();
    var bitmap = await CanvasBitmap.LoadAsync(device, stream);

    var renderer = new CanvasRenderTarget(device, 
                                          bitmap.SizeInPixels.Width, 
                                          bitmap.SizeInPixels.Height, bitmap.Dpi);

    using (var ds = renderer.CreateDrawingSession())
    {
        var blur = new GaussianBlurEffect();
        blur.BlurAmount = 5.0f;
        blur.Source = bitmap;
        ds.DrawImage(blur);
    }

    stream.Seek(0);
    await renderer.SaveAsync(stream, CanvasBitmapFileFormat.Png);

    BitmapImage image = new BitmapImage();
    image.SetSource(stream);
    paneBackground.ImageSource = image;
}

```

So take the stream from the previous step, pass it to Win2D for some magical processing, and grab a refreshing drink because your work is done. 

In the above animation, I used this exact same code to apply the blured image to a SplitView.Pane Background right before the pane is opened (the content of the SplitView is just a WebView because I'm too lazy to create real UI). It is trivial to combine multiple effects for super cool experiences and there are thousands of variations. I'd love to see what you create with this.

## Resources ##
* [RenderTargetBitmap](https://msdn.microsoft.com/en-us/library/windows/apps/windows.ui.xaml.media.imaging.rendertargetbitmap)
* [Win2D Github](https://github.com/Microsoft/Win2D)
* [Win2D Blog](http://blogs.msdn.com/b/win2d/)
* [Introducing Win2D: DirectX-Powered Drawing in C# - Build 2015](https://channel9.msdn.com/Events/Build/2015/2-631)
 
Keep hacking my friends!
