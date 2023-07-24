# VSharedData
- One plugin for PocketMine-PMMP 5
- It's both a plugin as well as a small management for your server network.

# Feature (Plugin)
- Help your server network reduce memory consumption for the base that hosts them.
- Save inventory player data for a network of servers that require shared player data.
- Help your server network to share a worlds folder, plugins and inventory player.

# Note (Plugin)
- Note that this plugin cannot run plugins such as DEVirion and PocketMine-DevTools in the directory that you create to run its plugins.

# Config (Plugin)
```
---

# Worlds path
worlds-path: 'C:\Users\Nam\Desktop\SharedData\worlds'

# Plugins path
plugins-path: 'C:\Users\Nam\Desktop\SharedData\plugins'

# Inventory players
inventory-players:

  # Enable inventory players path
  enable: true

  # Path
  path: 'C:\Users\Nam\Desktop\SharedData\inventory-players'

...
```

# How to setup? (Plugin)
- Please create any folder with the name according to your preference.
- Then create folders that help save player inventory and the worlds you need to download and plugins
- Example:
<img src="https://github.com/VennDev/VSharedData/blob/main/images/1.png" alt="VMiningSack" height="300" width="750" />
<img src="https://github.com/VennDev/VSharedData/blob/main/images/2.png" alt="VMiningSack" height="300" width="750" />
<img src="https://github.com/VennDev/VSharedData/blob/main/images/3.png" alt="VMiningSack" height="300" width="750" />
- Video setup: https://youtu.be/han5XgF2Ts0

# Methods (Plugin)
- You can load the world on another server, but only one server can load it.
- You can logically handle them in a necessary way.
- For example, I have 2 servers as below:
```php
# Server Skyblock A
# Let's say I need to load a world for player A and teleport them to.
if (VSharedData::loadWorld('IslandPlayerA')) {
  //TODO: Your Logic
}

# This is when they leave the server or leave their world.
if (VSharedData::isWorldLoaded('IslandPlayerA')) {
  VSharedData::unloadWorld('IslandPlayerA'); //Stop loading the world to return it to another server that can use it if needed.
}
```
- With this, you can handle a server with a large network of managing worlds easily.

# The exception (This is a non-plugin item)
- When you go to the [folder manager](https://github.com/VennDev/VSharedData/tree/main/manager) I have provided in this plugin directory. It will help you manage which server is running and running it.
- You can setup it in 2 .bat files that I left inside.
- Just support: windows & windows server
- Example setup:
<img src="https://github.com/VennDev/VSharedData/blob/main/images/4.png" alt="VMiningSack" height="300" width="750" />
- Video setup: https://youtu.be/han5XgF2Ts0
