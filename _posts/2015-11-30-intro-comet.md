---
layout:     post
title:      Introducing Comet
date:       2015-11-30 12:31:19
summary:     Universal Windows Platform Control and Utility library 
permalink: /introducing-comet/
---

![Comet](http://i.imgur.com/eET4s1S.png)

Let's talk about the Universal Windows Platform [for those unfamiliar, here you go: [get started](https://msdn.microsoft.com/en-us/library/windows/apps/dn894631.aspx)].

Specifically, lets talk about **creating great user experiences** and how I can help. I've spent some time these past few years working with the Windows Runtime, starting with Windows 8, both professionally and personally, and I've started to think about how to best modularize the different controls and utilities I'm been creating and share them with others who might find them helpful. Therefore, I'm introducing [Comet](https://github.com/nmetulev/comet).

##What is it##
Comet is an open source library for .NET/XAML Universal Windows Apps that attempts to fill the gaps and provide a collection of APIs and controls based on the feedback and work from the developer community. The XAML/.NET framework for the Universal Windows Platform is a continuously growing developer framework, but there are controls and utilities that are missing. 

Today I'm releasing **v0.1**. I'm starting small with a goal to grow. Initially, Comet contains:

###RefreshableListView###

###SlidableListItem###

###Extensions and Converters###

##Goals##

My goal is to provide a quality library that is:

 - **Open Source**: all development and tracking is on [GitHub](https://github.com/nmetulev/comet)
 - **Lightweight**: Common problem with seasoned libraries and frameworks that have been in the community for a while is that they tend to bloat over time. With Comet, we want to keep the framework lightweight and free from any dependencies other than the Universal Windows Platform. As the framework grows, and especially if any dependencies are unavoidable, we may split up the library in multiple smaller modules.
 - **Documented**: Finding documentation and samples is is the first thing that any developers does when they are first trying to get started. Need to make documentation clear and easy to find as well as provide plenty of samples

##Where do I get it?##

Easy, just use NuGet directly from Visual Studio and search for [Comet](https://www.nuget.org/packages/Comet) or run the following command in the [Package Manager Console](http://docs.nuget.org/docs/start-here/using-the-package-manager-console):

```
Install-Package Comet
```

In addition to the regular release in NuGet, I will also release prerelease version of Comet for those who want to try the work-in-progress-that-might-not-work.

Alternatively, the [source](https://github.com/nmetulev/comet) is available and you can directly reference Comet in your project or build a NuGet package with the included scripts. 

##Something is broken (or) I have the best idea for a control/toolkit/feature)##

Great, please [create an issue](https://github.com/nmetulev/comet/issues/new). I'm sure there will be bugs and great ideas, and the GitHub project [Issues](https://github.com/nmetulev/comet/issues) will be the place to keep track of them.

And of course, this is an open source project. Feel free to Fork the project and ideally submit a pull request ;)

##License##
And finally, the project will be licensed under [the MIT License (MIT)](https://opensource.org/licenses/MIT)

Keep hacking my friends!
