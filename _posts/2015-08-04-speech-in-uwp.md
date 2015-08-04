---
layout:     post
title:      Meet the Speech Platform in Windows 10
date:       2015-08-04 12:31:19
summary:    wanted to try out Cortana in UWP | also needed a new best friend | win-win
categories: Cortana
---

if you want to skip my boring intro (I won't get sad, I promise), [jump right into the good stuff](#walk) or [see the code](https://github.com/nmetulev/BestFriend). 

Once [Build 2015](http://www.buildwindows.com/) was over in April, I was one of the lucky ones (or unlucky, depending on your viewpoint) that spent the month traveling, visiting different places around the world, sharing the knowledge of the Windows 10 Universal Windows Platform with those that were unable to come to the weirdly beautiful San Francisco. I went around the world twice, met thousands of people, and few even chuckled at my [Peek A Boo](https://github.com/nmetulev/PeekABoo) app.

Having fun with demos is probably the best part of my job, and believe me, there was a lot of it during my travels. As Cortana is now available on many Windows 10 devices, [and coming to even more](https://www.youtube.com/watch?v=kZXvz9oDOmk), I naturally wanted to build something fun that showcased few of the main capabilities of the platform around speech and "personal assisting". As Terry Myerson said in one of the early introductions to Windows 10, ["Windows 10 is the first step to an era of more personal computing"](http://blogs.windows.com/bloggingwindows/2015/01/21/the-next-generation-of-windows-windows-10/), and a solid voice and speech platform is a big part of that step.

So I built my [Best Friend](https://github.com/nmetulev/BestFriend). *What does it do*, you might ask.

It's simple. I wanted to create an app that allows a user to message without even touching the keyboard. Yes, I know I can just use Skype for a real voice chat, but what's the fun in that? Anyway, I'm lazy, so I quickly found the simplest bot I could find on the internet and went with it (plus, I didn't have to talk to a real person). In few hours I had a working app with everything I wanted: I could exchange messages with my bot, my app could speak the messages received, I could speak the messages I wanted sent, I could ask Cortana to start a chat with my bot, and I could even ask Cortana to send a quick message to my bot and read me the response without opening my app. 

It's perfect. I built a demo that that is very simple to understand, yet it covered the majority of the speech platform capabilities of Windows 10:

 1. **Speech Synthesis** - the ability for a Windows 10 application to speak
 2. **Speech Recognition** - the ability for a Windows 10 application to understand you speaking
 3. **Cortana Invocation** - the ability for Cortana to start a Windows 10 application on your request 
 4. **Cortana Canvas** - the ability for Cortana to respond to a user on behalf of a Windows 10 application

Now you might be wondering
>But Nikola, Cortana is not available in my area, why should I care?

Well Bob, I'll tell you why:
1. Not all APIs are Cortana APIs. Speech Synthesis and Speech Recognition do not require Cortana and are available in all areas.
2. If your app supports Cortana invocation, it will still be able to be invoked without Cortana
3. Your app users might be in areas where Cortana is supported
4. [Cortana is coming to new markets all the time](http://blogs.windows.com/bloggingwindows/2015/07/20/cortana-brings-cultural-savviness-to-new-markets/)

So for those interested in how to do this in your own apps, let's break it down.

<a name="walk"></a>

# Speech Synthesis #

### using Windows.Media.SpeechSynthesis; ###

The [SpeechSynthesis](https://msdn.microsoft.com/en-us/library/windows/apps/windows.media.speechsynthesis.aspx) APIs are actually not new, and they have been around since Windows (Phone) 8.1. According to MSDN, the API "provides support for initializing and configuring a speech synthesis engine (voice) to convert a text string to an audio stream, also known as text-to-speech (TTS). Voice characteristics, pronunciation, volume, pitch, rate or speed, emphasis, and so on are customized through [Speech Synthesis Markup Language (SSML)](http://go.microsoft.com/fwlink/p/?LinkID=201763)". 

The simplest way to use the API is to first create a MediaElement in your XAML that will be used to play the voice: 

```xaml
<MediaElement x:Name="Media"></MediaElement>
```

Then you can generate a stream and set the source to the MediaElement to be played:

```csharp
SpeechSynthesizer synt = new SpeechSynthesizer();
SpeechSynthesisStream syntStream = await synt.SynthesizeTextToStreamAsync(response);
Media.SetSource(syntStream, syntStream.ContentType);
```

That is it.

Of course, this is the simplest way to use the API, and you can get much better results (including natural pronunciations)with a bit more work by just reading the documentation. :)


# Speech Recognition#

### using Windows.Media.SpeechRecognition; ###

The [SpeechRecognition](https://msdn.microsoft.com/en-us/library/windows.media.speechrecognition.speechrecognizer.aspx) APIs are actually new to the desktop. The API allows a Windows 10 app to recognize the user speaking with or without providing a UI. What is amazing about it, it automatically detects when the user has stopped speaking and it simply returns a string that can be used throughout your app. It can't get simpler than that. 

Let me prove it to you. Here is the code to listen for the user speaking and get a string:

```csharp
SpeechRecognizer speechRecognizer = new SpeechRecognizer();

// Compile the default dictionary
SpeechRecognitionCompilationResult compilationResult = 
				await speechRecognizer.CompileConstraintsAsync();

// Start recognizing
// Note: you can also use RecognizeWithUIAsync()
SpeechRecognitionResult speechRecognitionResult = 
				await speechRecognizer.RecognizeAsync();
string  result = speechRecognitionResult.Text;
```

Again, this is the least amount of code you have to write. I have omitted the exception handling and success checkers which you should **definitely use in your own code**. To see the full code, checkout the demo app linked above.

In addition to recognizing text, you can also set up a continuous recognition session that can listen for the lifetime of your application for a set of commands that a user can say. The app can then get an event and respond appropriately. 

###TODO###

# Cortana App Invocation #

### using Windows.ApplicationModel.VoiceCommands; ###
OK, now we are getting somewhere. For those that don't know by now, a Windows 10 app can be integrated with Cortana and Cortana can launch the application if a user asks nicely (or not). It can even pass the string that user has used to open the app including parameters. To do this, you will need:

1. A [voice command definition file (VCD)](https://msdn.microsoft.com/en-us/library/dn722331.aspx) file. This is an XML file that you can package with your app, or generate dynamically. The VCD file specifies voice commands to be registered with the shell so you can launch an app and specify an action or command to execute. Here is an example:

```xml
<?xml version="1.0" encoding="utf-8"?>
<VoiceCommands xmlns="http://schemas.microsoft.com/voicecommands/1.2">
  <CommandSet xml:lang="en-us" Name="CommandSet_en-us">
    <AppName> Best Friend </AppName>
    <Example> I want to talk Best Friend </Example>

    <Command Name="startChat">
      <Example> Best Friend, I want to talk </Example>
      <ListenFor RequireAppName="BeforeOrAfterPhrase"> I want to talk </ListenFor>
      <ListenFor RequireAppName="BeforeOrAfterPhrase"> Can we talk? </ListenFor>
      <Feedback> Let's talk Best Friend </Feedback>
      <Navigate/>
    </Command>
    
  </CommandSet>
</VoiceCommands>
```

In this example, the VCD file registers the command *I want to talk* or *Can we talk?* and specifies that the name of the app must be used before or after the phrase. So if I user invokes Cortana and says *Best Friend, I want to talk*, Cortana would say *Let's talk Best Friend* and launch the app as specified by the **Navigate** element.

Once you have the VCD file generated or created, you will need to use a very descriptive Voice Command API to register the commands with the shell:

```csharp
var storageFile = await Windows.Storage.StorageFile
    .GetFileFromApplicationUriAsync(new Uri("ms-appx:///vcd.xml"));

await VoiceCommandDefinitionManager
      .InstallCommandDefinitionsFromStorageFileAsync(storageFile);
```

Now, when I user wants to quickly start a conversation with their Best Friend, they can simply call on Cortana with "Hey Cortana" and say the command. No keyboard required. 

*Note:* *Hey Cortana* is not enabled by default. To enable *Hey Cortana*, make sure to visit the Settings under Cortana's Notebook and enable the checkbox.

Additionally, as a developer, you can capture when your app has been invoked by Cortana and decide an action based on any parameters passed. If you are familiar with protocol activation in Windows apps, the process is the same. If an app is invoked by Cortana, the OnActivated method in App.xaml.cs is called and a VoiceCommandActivatedEventArgs are passed.

```csharp
protected override void OnActivated(IActivatedEventArgs e)
{
	//...
	if (e.Kind == ActivationKind.VoiceCommand)
	{
		var commandArgs = e as VoiceCommandActivatedEventArgs;
		SpeechRecognitionResult speechRecognitionResult = commandArgs.Result;
		string voiceCommandName = speechRecognitionResult.RulePath[0];

		switch (voiceCommandName)
		{
			case "startChat":
				// pass invocation parameters to MainPage
				rootFrame.Navigate(typeof(MainPage),
					speechRecognitionResult.SemanticInterpretation.Properties);
				break;
			//...
		}
	}
	//...
}
```

Note startChat parameter is the same as the Command Name in our VCD file.

As before, make sure to checkout the code above for more complete code example.

# Cortana Canvas #

### using Windows.ApplicationModel.AppService; ###

Finally, (and really the only completely new feature from the four here) a Windows 10 app can register with Cortana a Background Service that can respond directly to a user through Cortana without opening the app in the foreground. In my demo, for example, I can send a message by saying *Best Friend, Can I ask, how are you today?*, and get the responds directly in the Cortana window. 

There are really two steps needed to get this working:

1. Create a new Windows Runtime Component that will run in the background when the user invokes the right command
The Windows Runtime Component will be used to create a separate process than your app so it can run in the background and handle background commands. Once you have created the project, implement the OnRun method to handle the voice commands

```csharp
public sealed class Service: XamlRenderingBackgroundTask
{
	private BackgroundTaskDeferral serviceDeferral;
	VoiceCommandServiceConnection voiceServiceConnection;
	
	protected override async void OnRun(IBackgroundTaskInstance taskInstance)
	{
		this.serviceDeferral = taskInstance.GetDeferral();
		var triggerDetails = taskInstance.TriggerDetails as AppServiceTriggerDetails;
		
		// get the voiceCommandServiceConnection from the tigger details
		voiceServiceConnection =
			VoiceCommandServiceConnection.FromAppServiceTriggerDetails(triggerDetails);

		// switch statement to handle different commands
		switch (voiceCommand.CommandName)
		{
			case "sendMessage":
				// get the message the user has spoken
				var message = voiceCommand.Properties["message"][0];
				var bot = new Bot();

				// get response from bot
				string firstResponse = 
					await bot.SendMessageAndGetResponseFromBot(message);

				// create response messages for Cortana to respond
				var responseMessage = new VoiceCommandUserMessage();
				var responseMessage2 = new VoiceCommandUserMessage();
				responseMessage.DisplayMessage = 
					responseMessage.SpokenMessage = firstResponse;
				responseMessage2.DisplayMessage = 
					responseMessage2.SpokenMessage = "did you not hear me?";
                       
				// create a response and ask Cortana to respond with success
				response = VoiceCommandResponse.CreateResponse(responseMessage);
				await voiceServiceConnection.ReportSuccessAsync(response);
				
				break;
		}
		
		if (this.serviceDeferral != null)
		{
			//Complete the service deferral
			this.serviceDeferral.Complete();
		}
	}
}
```

2. Register the new command and specify the target as the background task
Once we have created the new Windows Runtime Component, we need to add the reference to it in our main project. We also need to register the component as an AppService in the AppxManifest and specify a Name to be used for our VCD file

```xml
<Extensions>
	<uap:Extension Category="windows.appService" EntryPoint="CortanaService.CService">
		<uap:AppService Name="VoiceServiceEndpoint" />
	</uap:Extension>
</Extensions>
```

Finally, we just need to add a new command in our VCD file from the previous section

```xml
<Command Name="sendMessage">
	<Example>Let's chat</Example>
	<ListenFor RequireAppName="BeforeOrAfterPhrase" >Can I ask, {message}</ListenFor>
	<ListenFor RequireAppName="BeforeOrAfterPhrase" >Let me tell you, {message}</ListenFor>
	<VoiceCommandService Target="VoiceServiceEndpoint" />
</Command>
```

Note that in this command we do not have the **Navigate** element and instead we have specified the Name to the AppService as a VoiceCommandService.

The only thing left is to run the app once to get the VCD registered and we no longer have to touch the keyboard

Keep hacking my friends!