---
layout:     post
title:      Introducing Comet
date:       2015-12-01 12:31:19
summary:    Universal Windows Platform Control and Utility library for great user experiences
permalink: /introducing-comet/
---

![Comet](http://i.imgur.com/NvyoRv0.png)

Let's talk about the Universal Windows Platform [for those unfamiliar, here you go: [get started](https://msdn.microsoft.com/en-us/library/windows/apps/dn894631.aspx)].

Specifically, lets talk about **creating great user experiences** and how I can. I've spent some time these past few years working with the Windows Runtime, starting with Windows 8, both professionally and personally, and I've started to think about how to best package the different controls and utilities I've been creating and share them with others who might find them helpful. 

Therefore, I'm introducing [Comet](https://github.com/nmetulev/comet).

## What is it? ##
Comet is an open source library for .NET/XAML Universal Windows Apps that attempts to fill the gaps and provide a collection of APIs and controls based on the feedback and work from the developer community. The XAML/.NET framework for the Universal Windows Platform is a continuously growing developer framework, but there are controls and utilities that are missing. 

Today I'm releasing **v0.1**. I'm starting small with a goal to grow. Initially, Comet contains:

### RefreshableListView ###

Pull to Refresh is one of the most used gestures on all mobile touch platforms, but it's not something that is provided out of box for XAML. I've seen many different implementations for XAML, and there is even an [official sample by Microsoft](https://github.com/Microsoft/Windows-universal-samples/tree/master/Samples/XamlPullToRefresh) of a simple implementation. However, I've never been satisfied by the performance, behavior, or functionality of these implementations. I created my own:

![Imgur](http://i.imgur.com/JMHGw6A.gif)

The above is from the [CometMailSample](https://github.com/nmetulev/comet/tree/master/Samples/Mail) that I've included with the source. I wanted to create a control that can be used to create rich and performant pull-to-refresh animations, but something that is easy to use. 

Here is a quick example (not the same one as above, but not much more complicated):

``` xaml
<c:PullToRefreshListView x:Name="listView"
                           ItemsSource="{x:Bind Items}" 
                           RefreshRequested="listView_RefreshCommand"
                           PullProgressChanged="listView_PullProgressChanged"> 
                           
    <c:PullToRefreshListView.ItemTemplate>
        <DataTemplate  x:DataType="data:Item">
            <TextBlock Text="{x:Bind Title}" />
        </DataTemplate>
    </c:PullToRefreshListView.ItemTemplate>
    
    <c:PullToRefreshListView.RefreshIndicatorContent>
        <Border HorizontalAlignment="Center" 
                x:Name="refreshindicator" 
                CornerRadius="30" 
                Height="20" 
                Width="20" ></Border>
    </c:PullToRefreshListView.RefreshIndicatorContent>
</c:PullToRefreshListView>
```

``` csharp
private void listView_PullProgressChanged(object sender, 
			             Comet.Controls.RefreshProgressEventArgs e)
{
    refreshindicator.Opacity = e.PullProgress;

    refreshindicator.Background = e.PullProgress < 1.0 ? 
        new SolidColorBrush(Colors.Red) : new SolidColorBrush(Colors.Blue);
}

```

### SlidableListItem ###

The Outlook UWP app has one of the best gesture pasterns, which allows quick actions to be performed by simply sliding an email left or right (a huge time saver). It's a pattern that has been used by many other apps, but not something that is easily done in XAML. It's a pattern that I wish other apps used. I created my own:

![Imgur](http://i.imgur.com/5dWc6Cs.gif)

As before, the above is part of the [CometMailSample](https://github.com/nmetulev/comet/tree/master/Samples/Mail) that I've included with the source. I wanted to make it customizable so any application can take advantage of the pattern, and make my life as a consumer much easer. :)

Here is a simplified example:

``` xaml
<DataTemplate x:Key="EmailsItemTemplate" x:DataType="data:Item">
    <c:SlidableListItem LeftIcon="Favorite" 
                        RightIcon="Delete" 
                        LeftLabel="Set Favourite" 
                        RightLabel="Delete" 
                        HorizontalAlignment="Stretch"
                        LeftBackground="Green" 
                        RightBackground="Red"
                        MouseSlidingEnabled="true"
                        LeftCommand="{x:Bind ToggleFavorite}"
                        RightCommandRequested="RightCommandRequested">
                        
        <Grid Height="110">
            <StackPanel Margin="10,0,0,0">
                <CheckBox IsChecked="{x:Bind IsFavorite, Mode=OneWay}"/>
                <TextBlock Text="{x:Bind Title}"
                           TextWrapping="NoWrap"/>
            </StackPanel>
        </Grid>
        
    </c:SlidableListItem>
</DataTemplate>
```

It's worth noting that this control is not required to be used in a ListView. It can be used as a standalone container :)

### Converters and  Extensions ###

#### Comet.Converters ####

 - **DateTimeFormatConverter**: Converter to convert DateTime to string with the specified format (parameter)
 - **HexToSolidColorBrushConverter**: Converts string conteining hex color of the form (#FFFFFFFF) to SolidColorBrush
 - **ValueWhenConverter**: Converter for returning a specific value when a value equals to the spcified Value, or returning otherwise

#### Comet.Extensions ####

XamlExtensions:

 - **UIElement.RenderToRandomAccessStream()**: Render a UIElement into a bitmap IRandomAccessStream
 - **UIElement.RenderToBitmapImage()**: Render a UIElement into a bitmap
 - **DependencyObject.FindChildren\<T\>()**: Traverses the Visual Tree and returns a list of elements of type T

## What is the goal? ##

The goal is to fill the gaps in the UWP .NET/XAML platform and provide a collection of APIs and controls based on the feedback and work from the developer community. 

The final result is a quality library that is:

 - **Open Source**: all development and tracking is on [GitHub](https://github.com/nmetulev/comet)
 - **Lightweight**: Common problem with seasoned libraries and frameworks that have been in the community for a while is that they tend to bloat over time. With Comet, we want to keep the framework lightweight and free from any dependencies other than the Universal Windows Platform. As the framework grows, and especially if any dependencies are unavoidable, we may split up the library in multiple smaller modules.
 - **Documented**: Finding documentation and samples is is the first thing that any developers does when they are first trying to get started. Need to make documentation clear and easy to find as well as provide plenty of samples

## Where do I get it? ##

Easy, just use NuGet directly from Visual Studio and search for [Comet](https://www.nuget.org/packages/Comet) or run the following command in the [Package Manager Console](http://docs.nuget.org/docs/start-here/using-the-package-manager-console):

```
Install-Package Comet
```

In addition to the regular release in NuGet, I will also release prerelease version of Comet for those who want to try the work-in-progress-that-might-not-work.

Alternatively, this is an [open source](https://github.com/nmetulev/comet) project after all  and you can directly reference Comet in your project or build a NuGet package with the included scripts. 

## Something is broken (or) I have the best idea for a control/toolkit/feature) ##

Great, please [create an issue](https://github.com/nmetulev/comet/issues/new). I'm sure there will be bugs and great ideas, and the GitHub project [Issues](https://github.com/nmetulev/comet/issues) will be the place to keep track of them.

And of course, this is an open source project. Feel free to fork the project and ideally submit a pull request ;)

## License ##
The project is licensed under [the MIT License (MIT)](https://opensource.org/licenses/MIT)

Keep hacking my friends!
