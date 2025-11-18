<?php
/**
 * ARK Server Manager - INI Key Descriptions
 * Comprehensive descriptions for all INI configuration keys
 * Include this file in your config.php: require_once 'KeyDescriptions.php';
 */

$INI_KEY_DESCRIPTIONS = [
    
    // ========================================
    // ENGINE.INI SETTINGS
    // ========================================
    
    // [/Script/Engine.GameNetworkManager]
    'MaxClientSmoothingDelta' => 'Maximum time delta for client position smoothing. Lower values = more responsive but jittery movement. Range: 0.05-0.2. Default: 0.1',
    'NetClientTicksPerSecond' => 'How many times per second the client processes network updates. Higher = smoother but more CPU usage. Range: 20-60. Default: 30',
    'ClientNetSendMoveDeltaTime' => 'Minimum time between client movement updates sent to server. Lower = more responsive. Range: 0.016-0.05. Default: 0.033',
    'ClientNetSendMoveThrottleTime' => 'Throttle time for client movement packets to prevent spam. Range: 0.03-0.1. Default: 0.05',
    
    // [/Script/OnlineSubsystemUtils.IpNetDriver] (Engine.ini)
    'MaxClientRate' => 'Maximum bytes per second the server sends to each client. Higher = less lag but more bandwidth. Range: 50000-500000. Default: 150000',
    'MaxInternetClientRate' => 'Maximum bandwidth for internet clients specifically. Same as MaxClientRate but for internet connections. Default: 150000',
    'InitialConnectTimeout' => 'Seconds before initial connection attempt times out. Increase for slow connections. Range: 30-300. Default: 150',
    'ConnectionTimeout' => 'Seconds of no response before client is kicked. Higher = more forgiving for lag spikes. Range: 60-300. Default: 180',
    'LanServerMaxTickRate' => 'Maximum server tick rate for LAN connections. Higher = smoother but more CPU usage. Range: 30-120. Default: 60',
    
    // [/Script/Engine.Player]
    'ConfiguredInternetSpeed' => 'Configured download speed for internet connections in bytes/sec. Affects download performance. Default: 150000',
    'ConfiguredLanSpeed' => 'Configured download speed for LAN connections in bytes/sec. Usually higher than internet. Default: 200000',
    
    // ========================================
    // GAME.INI SETTINGS
    // ========================================
    
    // [/Script/OnlineSubsystemUtils.IpNetDriver] (Game.ini)
    'NetServerMaxTickRate' => 'Maximum server ticks per second. Higher = smoother but more CPU. 30 is standard, 60 for high-end. Default: 30',
    'MaxNetTickRate' => 'Maximum network tick rate. Should match NetServerMaxTickRate. Range: 20-60. Default: 30',
    'MaxClientFrameRate' => 'Maximum client frame rate cap. Set to 0 for unlimited or match your refresh rate. Default: 60',
    'KeepAliveTime' => 'Seconds between keep-alive packets to prevent timeout. Lower = more network traffic. Range: 0.1-1.0. Default: 0.2',
    'RelevantTimeout' => 'Seconds before an actor becomes irrelevant and stops updating. Range: 60-300. Default: 180',
    'SpawnPrioritySeconds' => 'Priority time for spawning actors. Affects load distribution. Range: 0.5-2.0. Default: 1.0',
    'ServerTravelPause' => 'Pause duration in seconds when traveling between maps. Range: 2-10. Default: 4.0',
    'bUseSeamlessTravel' => 'Enable seamless map transitions without disconnection. true = smoother travel, false = disconnect and reconnect. Default: true',
    'MaxChannelSize' => 'Maximum size of a network channel in bytes. Higher = more complex actors. Range: 16384-65536. Default: 32767',
    'MaxPacketSize' => 'Maximum network packet size in bytes. Must match router MTU. Range: 512-1500. Default: 1400',
    'MaxPacketOverhead' => 'Bytes reserved for packet headers/overhead. Range: 64-256. Default: 128',
    'ReliableDataOverflowDelay' => 'Delay in seconds before retrying reliable data that overflowed. Range: 0.1-1.0. Default: 0.5',
    'NetConnectionTimeout' => 'Timeout for network connections in seconds. Higher = more forgiving for packet loss. Range: 60-300. Default: 180',
    'AckTimeout' => 'Timeout for packet acknowledgment in seconds. Lower = faster retransmission. Range: 0.5-2.0. Default: 1.0',
    'TimeoutMultiplierForUnoptimizedBuilds' => 'Multiplier for timeouts in debug/unoptimized builds. Range: 1-10. Default: 4.0',
    
    // [/script/shootergame.shootergamemode]
    'OverrideCaveTamingRestrictions' => 'Allow taming in caves when set to true. true = can tame anywhere, false = vanilla cave restrictions. Default: false',
    'DinoClassNameToAllowTameInCaves' => 'Specific dino classes allowed in caves. "All" = all dinos allowed. Use creature blueprint paths for specific dinos.',
    'bAllowPlatformSaddleBuildingInPvE' => 'Allow building on platform saddles in PvE. true = can build, false = no building on platforms. Default: false',
    'bAllowFlyerPlatformBuilding' => 'Allow building on flying platform saddles. true = can build on quetz/etc, false = no building. Default: false',
    'bFlyerPlatformAllowUnalignedDinoBasing' => 'Allow dinos on flying platforms without perfect alignment. true = more flexible placement. Default: false',
    'PerPlatformMaxStructuresMultiplier' => 'Multiplier for max structures on platform saddles. Higher = more structures allowed. Range: 0.1-20. Default: 1.0',
    'MaxActiveWildDinos' => 'Maximum wild dinos that can exist simultaneously. Higher = more wildlife but more lag. Range: 5000-50000. Default: 15000',
    'PerPlatformMaxActiveDinos' => 'Max dinos that can be placed on each platform saddle. Range: 100-10000. Default: 5000',
    'ClampItemStats' => 'Prevent items from exceeding stat limits. true = capped stats, false = unlimited scaling. Default: true',
    'PreventOfflinePvPInterval' => 'Minutes after logout before PvP protection activates. Range: 0-60. Default: 5',
    'bAllowUnlimitedRespecs' => 'Allow unlimited mindwipe/stat respecs. true = unlimited, false = vanilla limit. Default: false',
    'bDisableStructureDecayPvE' => 'Disable structure decay in PvE. true = no decay, false = structures decay over time. Default: false',
    'bPvEDisableFriendlyFire' => 'Disable friendly fire in PvE mode. true = can\'t hurt tribe/allies, false = can damage friendlies. Default: true',
    'bUseCorpseLocator' => 'Enable corpse locator beacon on death. true = shows death location, false = no marker. Default: false',
    'bShowCreativeMode' => 'Show creative mode option in menu. true = visible, false = hidden. Default: false',
    'bDisableImprintDinoBuff' => 'Disable imprint bonuses for dinos. true = no imprint buff, false = normal imprint system. Default: false',
    'bAllowSpeedLeveling' => 'Allow leveling movement speed stat. true = can level speed, false = speed locked. Default: false',
    'bAllowFlyerSpeedLeveling' => 'Allow leveling flyer speed specifically. true = can level flyer speed, false = locked (vanilla). Default: false',
    'bDisableFlyerCarryPvE' => 'Disable flyer carry in PvE. true = no picking up, false = can carry creatures/players. Default: false',
    'bDisableStructurePlacementCollision' => 'Disable collision checks for structure placement. true = can place through things, false = normal collision. Default: false',
    'bAllowPlatformSaddleMultiFloors' => 'Allow multiple floors on platform saddles. true = multi-level building, false = single floor. Default: false',
    'OverrideMaxExperiencePointsPlayer' => 'Maximum XP a player can earn. Set to high value for unlimited leveling. Default: 1000000',
    'OverrideMaxExperiencePointsDino' => 'Maximum XP a tamed dino can earn. Higher = more levels possible. Default: 1000000',
    'OverrideMaxExperiencePointsGeneric' => 'Maximum XP for generic entities. Default: 1000000',
    'PlayerCharacterStaminaDrainMultiplier' => 'Multiplier for player stamina drain. Lower = less drain. Range: 0.1-10. Default: 1.0',
    'PlayerCharacterHealthRecoveryMultiplier' => 'Multiplier for player health regeneration. Higher = faster healing. Range: 0.1-10. Default: 1.0',
    'DinoCountMultiplier' => 'Multiplier for dino spawn counts. Higher = more dinos spawning. Range: 0.1-5. Default: 1.0',
    'TamedDinoLimit' => 'Maximum tamed dinos per tribe. Range: 50-10000. Default: 5000',
    'StructureLimitMultiplier' => 'Multiplier for structure limits. Higher = more structures allowed. Range: 0.1-10. Default: 1.0',
    'MaxTribeLogs' => 'Maximum tribe log entries stored. Higher = more history but more memory. Range: 100-1000. Default: 400',
    'bDisableFriendlyFire' => 'Disable friendly fire entirely (PvP and PvE). true = no friendly fire, false = can damage allies. Default: false',
    'bDisableLootCrates' => 'Disable loot crate spawns. true = no crates, false = crates spawn normally. Default: false',
    'bIncreasePvPRespawnInterval' => 'Increase respawn time after PvP death. true = longer respawn, false = normal. Default: false',
    'bAutoPvETimer' => 'Automatically switch between PvE and PvP on schedule. true = timed PvP windows, false = static mode. Default: false',
    'MaxNumberOfPlayersInTribe' => 'Maximum players allowed in a single tribe. Range: 1-100. Default: 70',
    'MaxAlliancesPerTribe' => 'Maximum alliances a tribe can join. Range: 1-20. Default: 10',
    'MaxTribesPerAlliance' => 'Maximum tribes allowed in one alliance. Range: 2-20. Default: 10',
    'bPvEAllowTribeWar' => 'Allow tribes to declare war in PvE mode. true = can declare war, false = no tribe wars. Default: false',
    'bPvEAllowTribeWarCancel' => 'Allow canceling tribe wars in PvE. true = can cancel wars, false = wars are permanent. Default: false',
    'bAllowCustomRecipes' => 'Allow creation of custom recipes. true = can make custom food, false = disabled. Default: true',
    'GlobalPoweredBatteryDurabilityDecreasePerSecond' => 'Battery drain rate per second. Lower = batteries last longer. Range: 0.1-10. Default: 3.0',
    'bDisableGenesisMissions' => 'Disable Genesis missions. true = missions unavailable, false = missions work. Default: false',
    'bDisableDefaultMapItemSets' => 'Disable default map-specific item spawns. true = custom loot only, false = normal spawns. Default: false',
    'bDisableWorldBuffs' => 'Disable world buff events (2x weekends, etc). true = no buffs, false = buffs active. Default: false',
    'bEnableWorldBuffScaling' => 'Scale world buffs based on server settings. true = buffs adjust to rates, false = static buffs. Default: false',
    'AdjustableMutagenSpawnDelayMultiplier' => 'Multiplier for mutagen spawn delay. Lower = spawns faster. Range: 0.1-10. Default: 1.0',
    'BaseHexagonRewardMultiplier' => 'Multiplier for hexagon mission rewards. Higher = more hexagons. Range: 0.1-10. Default: 1.0',
    'HexagonCostMultiplier' => 'Multiplier for hexagon shop costs. Lower = cheaper items. Range: 0.1-10. Default: 1.0',
    'bUseDinoLevelUpAnimations' => 'Show level up animations for dinos. true = animations play, false = no animations. Default: true',
    'bAllowUnclaimDinos' => 'Allow unclaiming tamed dinos. true = can unclaim, false = cannot release ownership. Default: true',
    'StructureDamageRepairCooldown' => 'Cooldown in seconds between structure repairs. Range: 0-600. Default: 180',
    'PvPZoneStructureDamageMultiplier' => 'Damage multiplier for structures in PvP zones. Higher = structures take more damage. Range: 1-10. Default: 6.0',
    'bPassiveDefensesDamageRiderlessDinos' => 'Auto-turrets damage wild/riderless dinos. true = shoot all, false = only aggressive. Default: false',
    'bLimitTurretsInRange' => 'Limit number of turrets in a radius (soft cap). true = limited, false = unlimited. Default: false',
    'bHardLimitTurretsInRange' => 'Hard limit on turrets (cannot place more). true = hard cap, false = soft warning. Default: false',
    'bIgnoreStructuresPreventionVolumes' => 'Ignore no-build zones. true = can build anywhere, false = respect no-build areas. Default: false',
    'bGenesisUseStructuresPreventionVolumes' => 'Use Genesis-specific no-build zones. true = Genesis restrictions, false = ignore. Default: false',
    'DinoHarvestingDamageMultiplier' => 'Multiplier for dino harvesting damage/yield. Higher = more resources per hit. Range: 0.1-10. Default: 1.0',
    'PlayerHarvestingDamageMultiplier' => 'Multiplier for player harvesting damage/yield. Higher = more resources per swing. Range: 0.1-10. Default: 1.0',
    'GlobalSpoilingTimeMultiplier' => 'Multiplier for item spoilage time. Higher = food lasts longer. Range: 0.1-100. Default: 1.0',
    'GlobalItemDecompositionTimeMultiplier' => 'Multiplier for item decomposition in bags/corpses. Higher = lasts longer. Range: 0.1-100. Default: 1.0',
    'GlobalCorpseDecompositionTimeMultiplier' => 'Multiplier for corpse decomposition time. Higher = bodies last longer. Range: 0.01-100. Default: 1.0',
    'CropGrowthSpeedMultiplier' => 'Multiplier for crop growth speed. Higher = crops grow faster. Range: 0.1-100. Default: 1.0',
    'LayEggIntervalMultiplier' => 'Multiplier for egg laying interval. Lower = eggs laid more frequently. Range: 0.1-10. Default: 1.0',
    'MatingIntervalMultiplier' => 'Multiplier for mating cooldown. Lower = can breed more often. Range: 0.01-10. Default: 1.0',
    'EggHatchSpeedMultiplier' => 'Multiplier for egg hatching speed. Higher = hatches faster. Range: 0.1-100. Default: 1.0',
    'BabyMatureSpeedMultiplier' => 'Multiplier for baby maturation speed. Higher = grows up faster. Range: 0.1-1000. Default: 1.0',
    'BabyCuddleIntervalMultiplier' => 'Multiplier for cuddle/imprint interval. Lower = more frequent cuddles. Range: 0.01-10. Default: 1.0',
    'BabyImprintAmountMultiplier' => 'Multiplier for imprint percentage per cuddle. Higher = more imprint per cuddle. Range: 0.1-10. Default: 1.0',
    'BabyCuddleGracePeriodMultiplier' => 'Multiplier for cuddle grace period. Higher = more time to cuddle. Range: 0.1-100. Default: 1.0',
    'BabyCuddleLoseImprintQualitySpeedMultiplier' => 'Speed at which missed cuddles reduce imprint. Lower = less penalty. Range: 0.01-10. Default: 1.0',
    'UseCorpseLifeSpanMultiplier' => 'Multiplier for player corpse lifespan. Higher = corpse lasts longer. Range: 1-10000. Default: 1.0',
    'PreventCorpseDespawn' => 'Prevent player corpses from despawning entirely. true = never despawn, false = normal despawn. Default: false',
    'PerLevelStatsMultiplier_Player[0]' => 'Player Health per level multiplier. Index [0] = Health. Higher = more HP per level. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[1]' => 'Player Stamina per level multiplier. Index [1] = Stamina. Higher = more stamina per level. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[7]' => 'Player Weight per level multiplier. Index [7] = Weight. Higher = more carry capacity per level. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[10]' => 'Player Fortitude per level multiplier. Index [10] = Fortitude. Higher = more resistance per level. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[0]' => 'Tamed dino Health per level multiplier. Index [0] = Health. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[7]' => 'Tamed dino Weight per level multiplier. Index [7] = Weight. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed_Add[10]' => 'Additive bonus for tamed dino stats per level. Index [10] = various stats. Range: 0.1-10. Default: 0',
    'HarvestHealthMultiplier' => 'Multiplier for resource node health. Higher = nodes have more HP, yield more. Range: 0.1-100. Default: 1.0',
    'HarvestAmountMultiplier' => 'Direct multiplier for harvest yield. Higher = more resources per gather. Range: 0.1-100. Default: 1.0',
    'DeathBagDecayTime' => 'Seconds before death bag/corpse starts decaying. Higher = more time to recover. Range: 60-86400. Default: 3600',
    'HarvestResourceItemAmountClassMultipliers' => 'Multiplier for specific resource harvest amounts. Format: (ClassName="Item_C",Multiplier=2.0)',
    'ConfigOverrideItemMaxQuantity' => 'Override max stack size for specific items. Format: (ItemClassString="Item_C",Quantity=(MaxItemQuantity=1000))',
    'ConfigOverrideItemWeight' => 'Override weight for specific items. Format: (ItemClassString="Item_C",Weight=0.1)',
    'bDisableRailgunPVP' => 'Disable railgun usage in PvP. true = railgun disabled, false = railgun works. Default: false',
    
    // ========================================
    // GAMEUSERSETTINGS.INI SETTINGS
    // ========================================
    
    // [ServerSettings]
    'OverrideStructurePlatformPrevention' => 'Override restrictions on platform structure placement. true = less restrictions, false = vanilla. Default: false',
    'AutoSavePeriodMinutes' => 'Minutes between automatic server saves. Lower = more frequent saves but more disk I/O. Range: 5-60. Default: 15',
    'MaxClientSendsPerTick' => 'Maximum client updates per tick. Higher = more responsive but more bandwidth. Range: 10-100. Default: 40',
    'KickIdlePlayersPeriod' => 'Minutes before idle players are kicked. 0 = never kick idle. Range: 0-300. Default: 0',
    'ServerHardMaxNumPlayers' => 'Hard maximum number of players allowed. Cannot be exceeded. Range: 1-127. Default: 70',
    'ClampResourceHarvestDamage' => 'Clamp harvest damage to prevent exploit multipliers. true = capped, false = uncapped. Default: false',
    'MaxPingLimit' => 'Maximum ping before player is kicked (milliseconds). 0 = no limit. Range: 0-1000. Default: 0',
    'NetworkIdleTimeout' => 'Seconds of network idle before timeout. Range: 60-3600. Default: 900',
    'bRawSockets' => 'Use raw sockets for networking (better performance). true = raw sockets, false = standard. Default: false',
    'AllowedOrigins' => 'CORS allowed origins for web-based tools. "*" = allow all. Default: *',
    'bUseBattlEye' => 'Enable BattlEye anti-cheat. true = BattlEye on, false = disabled. Default: true',
    'bServerAllowAnsel' => 'Allow NVIDIA Ansel screenshot tool. true = enabled, false = disabled. Default: false',
    'ServerAutoForceRespawnWildDinosInterval' => 'Seconds between forced wild dino respawns (destroywilddinos). 0 = never. Range: 0-86400. Default: 0',
    'PvEDinoTurretDamageMultiplier' => 'Damage multiplier for turrets vs dinos in PvE. Range: 0.1-10. Default: 1.0',
    'MaxTamedDinos' => 'Maximum tamed dinos on the server (all tribes combined). Range: 1000-50000. Default: 5000',
    'ListenServerTetherDistanceMultiplier' => 'Distance multiplier for non-dedicated server tether. Range: 0.1-10. Default: 1.0',
    'ShowDebug' => 'Show debug information in logs. true = verbose logging, false = normal. Default: false',
    'XPMultiplier' => 'Multiplier for XP gained from all sources. Higher = level faster. Range: 0.1-100. Default: 1.0',
    'CraftXPMultiplier' => 'Multiplier for XP gained from crafting. Range: 0.1-100. Default: 1.0',
    'GenericXPMultiplier' => 'Multiplier for generic XP gains. Range: 0.1-100. Default: 1.0',
    'SpecialXPMultiplier' => 'Multiplier for special event XP. Range: 0.1-100. Default: 1.0',
    'AllowTekSuitPowersInGenesis' => 'Allow Tek suit powers in Genesis missions. true = powers work, false = disabled. Default: false',
    'PreventDownloadSurvivors' => 'Prevent downloading survivors from other servers. true = blocked, false = allowed. Default: false',
    'PreventDownloadItems' => 'Prevent downloading items from other servers. true = blocked, false = allowed. Default: false',
    'PreventDownloadDinos' => 'Prevent downloading dinos from other servers. true = blocked, false = allowed. Default: false',
    'PreventUploadSurvivors' => 'Prevent uploading survivors to other servers. true = blocked, false = allowed. Default: false',
    'PreventUploadItems' => 'Prevent uploading items to other servers. true = blocked, false = allowed. Default: false',
    'PreventUploadDinos' => 'Prevent uploading dinos to other servers. true = blocked, false = allowed. Default: false',
    'NoTributeDownloads' => 'Completely disable tribute/upload downloads. true = no downloads, false = downloads work. Default: false',
    'TributeItemExpirationSeconds' => 'Seconds before uploaded items expire. Range: 3600-604800. Default: 86400 (24 hours)',
    'TributeDinoExpirationSeconds' => 'Seconds before uploaded dinos expire. Range: 3600-604800. Default: 86400 (24 hours)',
    'TributeCharacterExpirationSeconds' => 'Seconds before uploaded characters expire. Range: 3600-604800. Default: 86400 (24 hours)',
    'ShowMapPlayerLocation' => 'Show player position on map. true = position visible, false = no position shown. Default: false',
    'AllowThirdPersonPlayer' => 'Allow third-person camera view. true = can use, false = first-person only. Default: false',
    'ServerCrosshair' => 'Show crosshair on server. true = crosshair visible, false = no crosshair. Default: false',
    'TheMaxStructuresInRange' => 'Maximum structures allowed in a specific radius. Higher = more dense building. Range: 1000-50000. Default: 10500',
    'StructurePreventResourceRadiusMultiplier' => 'Radius multiplier for resource blocking around structures. Higher = larger block radius. Range: 0.1-10. Default: 1.0',
    'TribeNameChangeCooldown' => 'Cooldown in minutes before tribe can change name again. Range: 1-1440. Default: 15',
    'PlatformSaddleBuildAreaBoundsMultiplier' => 'Multiplier for platform saddle build area size. Higher = larger build area. Range: 0.1-5. Default: 1.0',
    'StructurePickupTimeAfterPlacement' => 'Seconds after placement when structure can be picked up. Range: 0-3600. Default: 30',
    'StructurePickupHoldDuration' => 'Seconds to hold pickup key to retrieve structure. Range: 0-5. Default: 0.5',
    'AllowIntegratedSPlusStructures' => 'Allow Structures Plus integrated features. true = S+ features on, false = vanilla. Default: false',
    'AllowHideDamageSourceFromLogs' => 'Allow hiding damage sources in tribe logs. true = can hide, false = always show. Default: true',
    'MaxNumOfSaveBackups' => 'Number of save file backups to keep. Higher = more recovery options but more disk space. Range: 1-100. Default: 20',
    'RCONServerGameLogBuffer' => 'Buffer size for RCON game log in lines. Range: 100-10000. Default: 600',
    'AllowHitMarkers' => 'Show hit markers when damaging entities. true = markers visible, false = no markers. Default: true',
    'AllowCrateSpawnsOnTopOfStructures' => 'Allow supply crates to spawn on player structures. true = can spawn on structures, false = blocked. Default: false',
    'GreaterRiftActivationMultiplier' => 'Multiplier for rift activation requirements. Lower = easier to activate. Range: 0.1-10. Default: 1.0',
    'ShowAnniversaryContent' => 'Show anniversary event content. true = event active, false = disabled. Default: true',
    'ServerPassword' => 'Password required to join server. Leave blank for no password. Default: (empty)',
    'ServerAdminPassword' => 'Password for admin access. Required for admin commands. Default: (empty)',
    'SpectatorPassword' => 'Password for spectator mode access. Leave blank to disable spectators. Default: (empty)',
    'RCONEnabled' => 'Enable RCON remote console. true = RCON on, false = disabled. Default: false',
    'RCONPort' => 'Port for RCON connections. Range: 1024-65535. Default: 27020',
    'AdminLogging' => 'Log all admin commands to file. true = log commands, false = no logging. Default: false',
    'ServerLogging' => 'Enable detailed server logging. true = verbose logs, false = minimal. Default: true',
    'bShowAdminCommands' => 'Show when admins use commands in chat. true = visible, false = hidden. Default: false',
    'ActiveMods' => 'Comma-separated list of mod IDs to load. Format: 123456,789012,345678. Default: (empty)',
    'TribeLogDestroyedEnemyStructures' => 'Log enemy structure destruction in tribe log. true = logged, false = not logged. Default: true',
    'bFilterTribeNames' => 'Filter profanity from tribe names. true = filtered, false = unfiltered. Default: false',
    'bFilterCharacterNames' => 'Filter profanity from character names. true = filtered, false = unfiltered. Default: false',
    'bFilterChat' => 'Filter profanity from chat messages. true = filtered, false = unfiltered. Default: false',
    'AllowSharedConnections' => 'Allow multiple connections from same IP. true = allowed, false = one per IP. Default: true',
    'ServerHardcore' => 'Enable hardcore mode (permadeath). true = character deleted on death, false = normal. Default: false',
    'ServerPVE' => 'Enable PvE mode. true = PvE, false = PvP. Default: false',
    'AllowCaveBuildingPvE' => 'Allow building in caves during PvE. true = can build, false = cannot build. Default: false',
    'AllowCaveBuildingPvP' => 'Allow building in caves during PvP. true = can build, false = cannot build. Default: true',
    'EnableExtraStructurePreventionVolumes' => 'Enable additional no-build zones. true = more restrictions, false = less. Default: false',
    'CrossARKAllowForeignDinoDownloads' => 'Allow downloading dinos from other cluster servers. true = allowed, false = blocked. Default: false',
    'PreventOfflinePvP' => 'Prevent PvP when tribe members are offline. true = offline protection, false = always vulnerable. Default: false',
    'PreventTribeAlliances' => 'Prevent tribes from forming alliances. true = no alliances, false = alliances allowed. Default: false',
    'PreventDiseases' => 'Prevent disease mechanics (swamp fever, etc). true = no diseases, false = diseases active. Default: false',
    'NonPermanentDiseases' => 'Make diseases temporary instead of permanent. true = temporary, false = permanent. Default: false',
    'PreventSpawnAnimations' => 'Disable spawn-in animations. true = instant spawn, false = normal animations. Default: false',
    'MaxGateFrameOnSaddles' => 'Maximum gate frames allowed on platform saddles. Range: 0-10. Default: 2',
    'RandomSupplyCratePoints' => 'Randomize supply crate spawn locations. true = random, false = fixed locations. Default: false',
    'MaxHexagonsPerCharacter' => 'Maximum hexagons a character can hold. Range: 100000-2000000000. Default: 2000000000',
    'UseFjordurTraversalBuff' => 'Enable Fjordur map traversal buff. true = buff active, false = disabled. Default: true',
    'globalVoiceChat' => 'Enable global voice chat. true = all players can hear, false = proximity only. Default: false',
    'proximityChat' => 'Enable proximity-based voice chat. true = local area only, false = disabled. Default: false',
    'alwaysNotifyPlayerLeft' => 'Always show notifications when players leave. true = always show, false = tribe only. Default: false',
    'DontAlwaysNotifyPlayerJoined' => 'Don\'t always show join notifications. true = hide some, false = show all. Default: false',
    'ServerForceNoHud' => 'Force disable HUD for all players. true = no HUD, false = HUD available. Default: false',
    'EnablePVPGamma' => 'Allow gamma adjustments in PvP. true = allowed, false = blocked. Default: false',
    'DisablePvEGamma' => 'Disable gamma adjustments in PvE. true = disabled, false = allowed. Default: false',
    'ShowFloatingDamageText' => 'Show floating damage numbers. true = visible, false = hidden. Default: false',
    'AllowFlyerCarryPVE' => 'Allow flyers to carry in PvE. true = can carry, false = cannot carry. Default: false',
    'DinoCharacterFoodDrainMultiplier' => 'Multiplier for dino food consumption. Lower = eat less often. Range: 0.1-10. Default: 1.0',
    'AllowRaidDinoFeeding' => 'Allow feeding dinos during raids. true = can feed, false = cannot feed. Default: false',
    'DisableDinoDecayPvE' => 'Disable tamed dino decay in PvE. true = no decay, false = dinos decay if abandoned. Default: false',
    'PvPDinoDecay' => 'Enable dino decay in PvP. true = dinos decay, false = no decay. Default: false',
    'AutoDestroyDecayedDinos' => 'Automatically destroy fully decayed dinos. true = auto-destroy, false = manual cleanup. Default: false',
    'MaxPersonalTamedDinos' => 'Maximum personally owned dinos (non-tribe). Range: 10-10000. Default: 150',
    'PersonalTamedDinosSaddleStructureCost' => 'Structure cost for personal dino saddles. Range: 1-100. Default: 19',
    'DisableImprintDinoBuff' => 'Disable imprint stat bonuses. true = no bonus, false = normal bonuses. Default: false',
    'AllowAnyoneBabyImprintCuddle' => 'Allow anyone to imprint babies. true = anyone can cuddle, false = only tribe. Default: false',
    'TamingSpeedMultiplier' => 'Multiplier for taming speed. Higher = faster taming. Range: 0.1-100. Default: 1.0',
    'TamingAffinitySpeedMultiplier' => 'Multiplier for taming affinity gain. Higher = faster taming. Range: 0.1-100. Default: 1.0',
    'ResourcesRespawnPeriodMultiplier' => 'Multiplier for resource respawn time. Lower = respawns faster. Range: 0.1-10. Default: 1.0',
    'UseOptimizedHarvestingHealth' => 'Use optimized harvesting calculations. true = optimized, false = legacy. Default: true',
    'ClampItemSpoilingTimes' => 'Clamp spoilage times to prevent exploits. true = clamped, false = unclamped. Default: false',
    'DayCycleSpeedScale' => 'Speed of day/night cycle. Lower = slower days. Range: 0.1-10. Default: 1.0',
    'PvPStructureDecay' => 'Enable structure decay in PvP. true = structures decay, false = no decay. Default: false',
    'DisableStructureDecayPVE' => 'Disable structure decay in PvE. true = no decay, false = decay enabled. Default: false',
    'ForceAllStructureLocking' => 'Force all structures to be lockable. true = all can lock, false = normal. Default: false',
    'OnlyAutoDestroyCoreStructures' => 'Only auto-destroy foundations/pillars on decay. true = core only, false = all structures. Default: false',
    'OnlyDecayUnsnappedCoreStructures' => 'Only decay unsnapped core structures. true = unsnapped only, false = all. Default: false',
    'FastDecayUnsnappedCoreStructures' => 'Faster decay for unsnapped structures. true = fast decay, false = normal. Default: false',
    'DestroyUnconnectedWaterPipes' => 'Destroy water pipes not connected to source. true = destroy, false = keep. Default: false',
    'IgnoreLimitMaxStructuresInRangeTypeFlag' => 'Ignore structure type limits in range. true = ignore, false = enforce. Default: false',
    'AlwaysAllowStructurePickup' => 'Always allow picking up structures. true = always pickup, false = time limit. Default: false',
    'PlayerCharacterFoodDrainMultiplier' => 'Multiplier for player food drain. Lower = less hunger. Range: 0.1-10. Default: 1.0',
    'PlayerCharacterWaterDrainMultiplier' => 'Multiplier for player water drain. Lower = less thirst. Range: 0.1-10. Default: 1.0',
    'bUseVSync' => 'Enable vertical sync. true = VSync on, false = VSync off. Default: false',
    'bUseDynamicConfig' => 'Use dynamic configuration reloading. true = configs reload, false = restart required. Default: false',
    'DisableStructurePlacementCollision' => 'Disable structure placement collision. true = place through objects, false = normal collision. Default: false',
    'AllowCropPlotStacking' => 'Allow stacking crop plots. true = can stack, false = cannot stack. Default: false',
    'DifficultyOffset' => 'Base difficulty offset. Affects dino levels. Range: 0.0-1.0. Default: 0.0',
    'bOverrideOfficialDifficulty' => 'Override official difficulty calculation. true = use custom, false = use offset. Default: false',
    'OverrideOfficialDifficulty' => 'Custom difficulty value (overrides offset). Max dino level = value * 30. Range: 1-10. Default: 5.0',
    'EnableCryopodNerf' => 'Enable cryopod nerf/cooldown. true = nerf active, false = no nerf. Default: false',
    'EnableCryosicknessPVE' => 'Enable cryosickness in PvE. true = sickness active, false = no sickness. Default: true',
    'CryopodNerfDuration' => 'Duration of cryopod nerf in seconds. Range: 0-3600. Default: 600',
    'CryopodNerfDamageMult' => 'Damage multiplier during cryosickness. Range: 0.0-10.0. Default: 4.0',
    'CryopodNerfIncomingDamageMultPercent' => 'Incoming damage percent during cryosickness. Range: 0-1000. Default: 400',
    'DisableCryopodEnemyCheck' => 'Disable cryopod enemy check (throw near enemies). true = can throw anywhere, false = blocked near enemies. Default: false',
    'AllowCryoFridgeOnSaddle' => 'Allow cryofridges on platform saddles. true = allowed, false = not allowed. Default: true',
    'DisableCryopodFridgeRequirement' => 'Disable requirement for cryofridge to charge cryopods. true = no fridge needed, false = fridge required. Default: false',
    'OxygenSwimSpeedStatMultiplier' => 'Multiplier for oxygen affecting swim speed. Range: 0.0-10.0. Default: 1.0',
    'RaidDinoCharacterFoodDrainMultiplier' => 'Food drain for dinos during raids. Range: 0.1-10. Default: 1.0',
    'PvEDinoDecayPeriodMultiplier' => 'Multiplier for dino decay period in PvE. Higher = longer before decay. Range: 0.1-10. Default: 1.0',
    'PerPlatformMaxStructuresMultiplier' => 'Multiplier for structures on platforms. Higher = more structures. Range: 0.1-20. Default: 1.0',
    'ItemStackSizeMultiplier' => 'Multiplier for item stack sizes. Higher = larger stacks. Range: 0.1-100. Default: 1.0',
    'BabyFoodConsumptionSpeedMultiplier' => 'Multiplier for baby food consumption. Lower = eat less. Range: 0.1-10. Default: 1.0',
    
    // [/Script/ShooterGame.ShooterGameUserSettings]
    'MasterAudioVolume' => 'Master volume level. Range: 0.0-1.0. Default: 1.0',
    'MusicAudioVolume' => 'Music volume level. Range: 0.0-1.0. Default: 1.0',
    'SFXAudioVolume' => 'Sound effects volume. Range: 0.0-1.0. Default: 1.0',
    'VoiceAudioVolume' => 'Voice chat volume. Range: 0.0-2.0. Default: 1.0',
    'CharacterAudioVolume' => 'Character sounds volume. Range: 0.0-1.0. Default: 1.0',
    'UIScaling' => 'User interface scaling. Range: 0.5-2.0. Default: 1.0',
    'UIQuickbarScaling' => 'Quickbar/hotbar scaling. Range: 0.5-2.0. Default: 1.0',
    'CameraShakeScale' => 'Camera shake intensity. Range: 0.0-1.0. Default: 1.0',
    'bFirstPersonRiding' => 'Force first-person when riding. true = first-person only, false = can switch. Default: false',
    'bThirdPersonPlayer' => 'Default to third-person view. true = third-person, false = first-person. Default: false',
    'bInventoryHideUnlearnedEngrams' => 'Hide unlearned engrams in inventory. true = hidden, false = shown. Default: false',
    'bShowStatusNotificationMessages' => 'Show status notifications. true = show messages, false = hide. Default: true',
    'TrueSkyQuality' => 'Sky rendering quality. Range: 0.0-1.0. Default: 0.7',
    'FOVMultiplier' => 'Field of view multiplier. Range: 0.5-1.5. Default: 1.0',
    'GroundClutterDensity' => 'Ground clutter (grass, rocks) density. Range: 0.0-2.0. Default: 1.0',
    'bFilmGrain' => 'Enable film grain effect. true = enabled, false = disabled. Default: false',
    'bMotionBlur' => 'Enable motion blur. true = enabled, false = disabled. Default: true',
    'bUseDistanceFieldAmbientOcclusion' => 'Use distance field AO (better shadows). true = enabled, false = disabled. Default: false',
    'bUseSSAO' => 'Use screen space ambient occlusion. true = enabled, false = disabled. Default: true',
    'bShowChatBox' => 'Show chat box. true = visible, false = hidden. Default: true',
    'bCameraViewBob' => 'Enable camera head bobbing. true = bobbing, false = steady. Default: true',
    'bInvertLookY' => 'Invert vertical look controls. true = inverted, false = normal. Default: false',
    'bFloatingNames' => 'Show floating player names. true = visible, false = hidden. Default: true',
    'bChatBubbles' => 'Show chat bubbles above players. true = bubbles, false = chat box only. Default: true',
    'bHideServerInfo' => 'Hide server info display. true = hidden, false = visible. Default: false',
    'bJoinNotifications' => 'Show join/leave notifications. true = show, false = hide. Default: true',
    'bCraftablesShowAllItems' => 'Show all craftable items regardless of materials. true = show all, false = only craftable. Default: false',
    'bLocalInventoryItemsShowAllItems' => 'Show all items in local inventory filters. true = show all, false = filtered. Default: false',
    'bLocalInventoryCraftingShowAllItems' => 'Show all items in local crafting. true = show all, false = only available. Default: true',
    'bRemoteInventoryItemsShowAllItems' => 'Show all items in remote inventory. true = show all, false = filtered. Default: false',
    'bRemoteInventoryCraftingShowAllItems' => 'Show all remote crafting items. true = show all, false = filtered. Default: false',
    'bRemoteInventoryShowEngrams' => 'Show engrams in remote inventory. true = visible, false = hidden. Default: true',
    'LookLeftRightSensitivity' => 'Horizontal look sensitivity. Range: 0.1-5.0. Default: 1.0',
    'LookUpDownSensitivity' => 'Vertical look sensitivity. Range: 0.1-5.0. Default: 1.0',
    'GraphicsQuality' => 'Overall graphics quality preset. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 2',
    'ClientNetQuality' => 'Client network quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    
    // [ScalabilityGroups]
    'sg.ResolutionQuality' => 'Resolution quality percentage. Range: 50-200. Default: 100',
    'sg.ViewDistanceQuality' => 'View distance quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.AntiAliasingQuality' => 'Anti-aliasing quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.ShadowQuality' => 'Shadow rendering quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.PostProcessQuality' => 'Post-processing effects quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.TextureQuality' => 'Texture quality and resolution. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.EffectsQuality' => 'Visual effects quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.TrueSkyQuality' => 'Sky rendering quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.GroundClutterQuality' => 'Ground clutter rendering quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.IBLQuality' => 'Image-based lighting quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 1',
    'sg.HeightFieldShadowQuality' => 'Terrain shadow quality. 0=Low, 1=Medium, 2=High, 3=Epic. Default: 3',
    'sg.GroundClutterRadius' => 'Radius for ground clutter rendering. Range: 1000-20000. Default: 10000',
    
    // [StructuresPlus]
    'RespectConfigOverrideItemMaxQuantity' => 'S+ respects custom stack sizes. true = uses custom stacks, false = S+ defaults. Default: true',
    'CompostBinCraftingSpeedMultiplier' => 'S+ compost bin speed multiplier. Higher = faster composting. Range: 0.1-100. Default: 1.0',
    'BeerBarrelCraftingSpeedMultiplier' => 'S+ beer barrel speed multiplier. Higher = faster brewing. Range: 0.1-100. Default: 1.0',
    'AllowPersonalOwnership' => 'Allow personal ownership of S+ structures. true = personal ownership, false = tribe only. Default: true',
    'LockStructures' => 'Auto-lock S+ structures on placement. true = auto-lock, false = unlocked. Default: false',
    
    // [/Script/Engine.GameSession]
    'MaxPlayers' => 'Maximum player slots. Must match ServerHardMaxNumPlayers. Range: 1-127. Default: 70',
    
    // [MultiHome]
    'MultiHome' => 'Enable multi-home networking for multiple IPs. true = multi-home, false = single IP. Default: false',
    
    // [MessageOfTheDay]
    'Message' => 'Message of the day shown to players on join. Text string, supports basic formatting.',
    'Duration' => 'Seconds to display MOTD. Range: 5-300. Default: 20',
    
    // [DinoTracker]
    'DisableWildTracking' => 'Disable tracking wild dinos. true = cannot track wild, false = can track. Default: false',
    'DisableWildFindButton' => 'Disable find button for wild dinos. true = button hidden, false = button available. Default: false',
    'DisableTribememberTracking' => 'Disable tracking tribe members. true = cannot track, false = can track. Default: false',
    'DisableDeathTracking' => 'Disable death location tracking. true = no death markers, false = death markers shown. Default: false',
    'DisableAllyTracking' => 'Disable tracking allied tribe members. true = cannot track allies, false = can track. Default: false',
    'DisableWildCoordinates' => 'Hide coordinates for wild dinos. true = hidden, false = shown. Default: false',
    
    // [/Game/PrimalEarth/CoreBlueprints/TestGameMode.TestGameMode_C]
    'bServerGameLogEnabled' => 'Enable server game logging. true = logging enabled, false = disabled. Default: false',
    'ConfigOverrideSupplyCrateItems' => 'Override loot tables for supply crates. Complex format - defines custom loot pools and drop chances.',
    
    // [SessionSettings]
    'SessionName' => 'Server name displayed in server browser. Text string - this is what players see.',
    
    // ========================================
    // ADDITIONAL COMMON SETTINGS NOT IN YOUR FILES
    // ========================================
    
    // Player Stats
    'PerLevelStatsMultiplier_Player[2]' => 'Player Oxygen per level multiplier. Index [2] = Oxygen. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[3]' => 'Player Food per level multiplier. Index [3] = Food. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[4]' => 'Player Water per level multiplier. Index [4] = Water. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[8]' => 'Player Melee Damage per level multiplier. Index [8] = Melee. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[9]' => 'Player Movement Speed per level multiplier. Index [9] = Speed. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[11]' => 'Player Crafting Speed per level multiplier. Index [11] = Crafting. Range: 0.1-10. Default: 1.0',
    
    // Dino Stats (Tamed)
    'PerLevelStatsMultiplier_DinoTamed[1]' => 'Tamed dino Stamina per level multiplier. Index [1] = Stamina. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[2]' => 'Tamed dino Oxygen per level multiplier. Index [2] = Oxygen. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[3]' => 'Tamed dino Food per level multiplier. Index [3] = Food. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[8]' => 'Tamed dino Melee Damage per level multiplier. Index [8] = Melee. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[9]' => 'Tamed dino Movement Speed per level multiplier. Index [9] = Speed. Range: 0.1-10. Default: 1.0',
    
    // Dino Stats (Wild)
    'PerLevelStatsMultiplier_DinoWild[0]' => 'Wild dino Health per level multiplier. Index [0] = Health. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoWild[1]' => 'Wild dino Stamina per level multiplier. Index [1] = Stamina. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoWild[7]' => 'Wild dino Weight per level multiplier. Index [7] = Weight. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoWild[8]' => 'Wild dino Melee Damage per level multiplier. Index [8] = Melee. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoWild[9]' => 'Wild dino Movement Speed per level multiplier. Index [9] = Speed. Range: 0.1-10. Default: 1.0',
    
    // Taming
    'DinoTurretDamageMultiplier' => 'Damage turrets do to dinos. Higher = more damage. Range: 0.1-10. Default: 1.0',
    'CustomRecipeEffectivenessMultiplier' => 'Effectiveness multiplier for custom recipes. Range: 0.1-10. Default: 1.0',
    'CustomRecipeSkillMultiplier' => 'Skill multiplier for crafting custom recipes. Range: 0.1-10. Default: 1.0',
    
    // Breeding
    'MatingSpeedMultiplier' => 'Speed multiplier for mating progress bar. Higher = faster mating. Range: 0.1-100. Default: 1.0',
    'BabyImprintingStatScaleMultiplier' => 'Scale of stat bonuses from imprinting. Range: 0.1-10. Default: 1.0',
    'WildDinoCharacterFoodDrainMultiplier' => 'Food drain for wild dinos. Range: 0.1-10. Default: 1.0',
    'TamedDinoCharacterFoodDrainMultiplier' => 'Food drain for tamed dinos. Range: 0.1-10. Default: 1.0',
    
    // Structure/Building
    'StructureDamageMultiplier' => 'Damage multiplier for structures. Higher = structures take more damage. Range: 0.1-10. Default: 1.0',
    'StructureResistanceMultiplier' => 'Resistance multiplier for structures. Higher = structures take less damage. Range: 0.1-10. Default: 1.0',
    'bAllowUnlimitedRespecs' => 'Allow unlimited character respecs. true = unlimited mindwipes, false = limited. Default: false',
    'bDisableDinoRiding' => 'Disable riding dinosaurs. true = cannot ride, false = can ride. Default: false',
    'bDisableDinoTaming' => 'Disable taming dinosaurs. true = cannot tame, false = can tame. Default: false',
    'bDisableDinoBreeding' => 'Disable dinosaur breeding. true = cannot breed, false = can breed. Default: false',
    
    // PvP/Combat
    'PlayerDamageMultiplier' => 'Damage players deal. Higher = more player damage. Range: 0.1-10. Default: 1.0',
    'PlayerResistanceMultiplier' => 'Damage resistance for players. Higher = players take less damage. Range: 0.1-10. Default: 1.0',
    'DinoDamageMultiplier' => 'Damage dinos deal. Higher = more dino damage. Range: 0.1-10. Default: 1.0',
    'DinoResistanceMultiplier' => 'Damage resistance for dinos. Higher = dinos take less damage. Range: 0.1-10. Default: 1.0',
    
    // Day/Night
    'DayTimeSpeedScale' => 'Speed of daytime passage. Higher = faster days. Range: 0.1-10. Default: 1.0',
    'NightTimeSpeedScale' => 'Speed of nighttime passage. Higher = faster nights. Range: 0.1-10. Default: 1.0',
    
    // Tribe/Governance
    'TribeSlotReuseCooldown' => 'Cooldown before tribe slot can be reused after leaving. Minutes. Range: 1-1440. Default: 1440 (24 hours)',
    'bAllowFlyingStaminaRecovery' => 'Allow stamina recovery while flying. true = can recover, false = no recovery. Default: false',
    
    // Server Performance
    'MaxStructuresOnSaddle' => 'Maximum structures on a single saddle platform. Range: 10-1000. Default: 40',
    'bAutoDestroyOldStructures' => 'Auto-destroy structures after decay timer. true = auto-destroy, false = manual. Default: false',
    'bAutoDestroyStructures' => 'Enable automatic structure destruction. true = enabled, false = disabled. Default: false',
    
    // Misc Gameplay
    'FuelConsumptionIntervalMultiplier' => 'Fuel consumption rate multiplier. Lower = fuel lasts longer. Range: 0.1-10. Default: 1.0',
    'AllowRaidDinoFeeding' => 'Allow feeding dinos during raids. true = can feed, false = cannot. Default: true',
    'AllowHitMarkers' => 'Show hit markers on damage. true = show markers, false = no markers. Default: true',
    
    // Caves & Artifacts
    'bAllowCaveBuildingPvE' => 'Allow building in caves (PvE). true = can build, false = cannot. Default: false',
    'bAllowCaveBuildingPvP' => 'Allow building in caves (PvP). true = can build, false = cannot. Default: true',
    
    // Boss & Events
    'KillXPMultiplier' => 'XP multiplier for kills. Higher = more XP per kill. Range: 0.1-100. Default: 1.0',
    'HarvestXPMultiplier' => 'XP multiplier for harvesting. Higher = more XP per gather. Range: 0.1-100. Default: 1.0',
    
    // Quality of Life
    'bAllowCaveBuildingPvE' => 'Allow building in caves during PvE. true = can build, false = blocked. Default: false',
    'bShowFloatingDamageText' => 'Show floating damage text. true = show numbers, false = hidden. Default: false',
    'bInfiniteStats' => 'Enable infinite stats mode (creative). true = infinite, false = normal. Default: false',
    'bImmortalWorld' => 'Make world immune to damage. true = indestructible, false = normal. Default: false',
    
    // Networking (Additional)
    'bServerUseDynamicConfig' => 'Server uses dynamic config reloading. true = hot reload, false = restart required. Default: false',
    'QueryPort' => 'Port for server queries (Steam). Range: 1024-65535. Default: 27015',
    'Port' => 'Main game port. Range: 1024-65535. Default: 7777',
    
    // Transfer/Upload System
    'MinimumDinoReuploadInterval' => 'Minimum seconds before re-uploading same dino. Range: 0-3600. Default: 0',
    'bClampItemStats' => 'Clamp item stats to prevent exploits. true = capped, false = unlimited. Default: true',
    
    // ========================================
    // ADVANCED GAME.INI SETTINGS
    // ========================================
    
    // Engram/Level System
    'OverridePlayerLevelEngramPoints' => 'Override engram points awarded at specific level. Format: OverridePlayerLevelEngramPoints=<level>,<points>',
    'OverrideEngramEntries' => 'Modify engram requirements. Format: OverrideEngramEntries=(EngramIndex=<num>,EngramHidden=<bool>,EngramPointsCost=<num>,EngramLevelRequirement=<num>)',
    'OverrideNamedEngramEntries' => 'Modify engrams by class name. Format: OverrideNamedEngramEntries=(EngramClassName="<name>",EngramHidden=<bool>,...)',
    'EngramEntryAutoUnlocks' => 'Auto-unlock engrams at specific levels. Format: EngramEntryAutoUnlocks=(EngramClassName="<name>",LevelToAutoUnlock=<num>)',
    'ExperiencePointsForLevel' => 'Custom XP required for each level. Format: ExperiencePointsForLevel[<level>]=<xp_amount>',
    'LevelExperienceRampOverrides' => 'Override XP curve formula. Format: LevelExperienceRampOverrides=(ExperiencePointsForLevel[<level>]=<xp>)',
    
    // Dino Spawn Customization
    'ConfigAddNPCSpawnEntriesContainer' => 'Add new dino spawns to locations. Complex format - adds creatures to spawn tables.',
    'ConfigSubtractNPCSpawnEntriesContainer' => 'Remove dino spawns from locations. Format: ConfigSubtractNPCSpawnEntriesContainer=(NPCSpawnEntriesContainerClassString="<container>",NPCSpawnEntries=...)',
    'ConfigOverrideNPCSpawnEntriesContainer' => 'Completely override dino spawns for a location. Replaces entire spawn table.',
    'NPCReplacements' => 'Replace one dino type with another globally. Format: NPCReplacements=(FromClassName="<old>",ToClassName="<new>")',
    'DinoSpawnWeightMultipliers' => 'Multiplier for specific dino spawn rates. Format: DinoSpawnWeightMultipliers=(DinoNameTag="<name>",SpawnWeightMultiplier=<num>)',
    'PreventDinoTameClassNames' => 'Prevent specific dinos from being tamed. Format: PreventDinoTameClassNames="<dino_class>"',
    
    // Loot/Supply Crate Customization
    'ConfigOverrideSupplyCrateItems' => 'Override supply crate loot tables. Extremely complex - defines what items appear in crates.',
    'ConfigAddSupplyCrateItems' => 'Add items to supply crates without replacing. Appends to existing loot.',
    'ConfigSubtractSupplyCrateItems' => 'Remove specific items from supply crates.',
    
    // Crafting Costs
    'ConfigOverrideCraftingCostItems' => 'Override crafting costs for items. Format: ConfigOverrideCraftingCostItems=(ItemClassString="<item>",BaseCraftingResourceRequirements=...)',
    'ConfigOverrideItemCraftingCosts' => 'Alternate format for overriding crafting costs.',
    
    // Harvesting Customization
    'HarvestResourceItemAmountClassMultipliers' => 'Multiplier for specific resource amounts. Format: (ClassName="<resource>",Multiplier=<num>)',
    'ConfigOverrideItemMaxQuantity' => 'Override max stack size. Format: (ItemClassString="<item>",Quantity=(MaxItemQuantity=<num>,bIgnoreMultiplier=<bool>))',
    
    // Player/Dino Stat Multipliers (Complete List)
    'PerLevelStatsMultiplier_Player[2]' => 'Player Oxygen per level. Index [2]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[3]' => 'Player Food per level. Index [3]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[4]' => 'Player Water per level. Index [4]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[5]' => 'Player Temperature Fortitude per level. Index [5]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[6]' => 'Player Torpidity per level. Index [6]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[8]' => 'Player Melee Damage per level. Index [8]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[9]' => 'Player Movement Speed per level. Index [9]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_Player[11]' => 'Player Crafting Speed per level. Index [11]. Range: 0.1-10. Default: 1.0',
    
    'PerLevelStatsMultiplier_DinoTamed[1]' => 'Tamed dino Stamina per level. Index [1]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[2]' => 'Tamed dino Oxygen per level. Index [2]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[3]' => 'Tamed dino Food per level. Index [3]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[4]' => 'Tamed dino Weight per level. Index [4]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[8]' => 'Tamed dino Melee Damage per level. Index [8]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoTamed[9]' => 'Tamed dino Movement Speed per level. Index [9]. Range: 0.1-10. Default: 1.0',
    
    'PerLevelStatsMultiplier_DinoWild[1]' => 'Wild dino Stamina per level. Index [1]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoWild[2]' => 'Wild dino Oxygen per level. Index [2]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoWild[3]' => 'Wild dino Food per level. Index [3]. Range: 0.1-10. Default: 1.0',
    'PerLevelStatsMultiplier_DinoWild[4]' => 'Wild dino Weight per level. Index [4]. Range: 0.1-10. Default: 1.0',
    
    // Additional Multipliers
    'MutagenLevelBoost' => 'Level boost from mutagen. Range: 0-100. Default: 5',
    'MutagenLevelBoost_Bred' => 'Level boost from mutagen for bred dinos. Range: 0-100. Default: 5',
    'FastDecayInterval' => 'Interval for fast decay checks in seconds. Range: 60-3600. Default: 43200',
    
    // PvP/Raid Settings
    'bPvPDisableFriendlyFire' => 'Disable friendly fire in PvP. true = no team damage, false = can damage allies. Default: false',
    'bFlyerPlatformAllowUnalignedDinoBasing' => 'Allow unaligned dinos on flyer platforms. true = flexible placement, false = strict. Default: false',
    
    // Tribe Settings
    'MaxTribeSize' => 'DEPRECATED - Use MaxNumberOfPlayersInTribe instead. Maximum players per tribe.',
    'bAllowTribeWar' => 'DEPRECATED in favor of bPvEAllowTribeWar. Allow tribe wars.',
    'bAllowTribeWarCancel' => 'DEPRECATED in favor of bPvEAllowTribeWarCancel. Allow war cancellation.',
    
    // Additional GameUserSettings.ini Options
    'ForceAllowCaveFlyers' => 'Allow flying in caves. true = can fly in caves, false = walking only. Default: false',
    'PreventDiseases' => 'Prevent all diseases (Swamp Fever, etc). true = no diseases, false = diseases enabled. Default: false',
    'NonPermanentDiseases' => 'Make diseases temporary instead of permanent. true = diseases can be cured naturally, false = permanent. Default: false',
    'AllowAnyoneBabyImprintCuddle' => 'Let anyone imprint babies, not just tribe. true = anyone, false = tribe/owner only. Default: false',
    'AllowUnlimitedRespec' => 'TYPO VERSION - Same as bAllowUnlimitedRespecs. Allow unlimited stat resets.',
    'bAutoUnlockAllEngrams' => 'Automatically unlock all engrams. true = all unlocked, false = must learn. Default: false',
    'bShowFloatingDamageText' => 'Show damage numbers. true = visible, false = hidden. Default: true',
    'bCrossARKAllowForeignDinoDownloads' => 'Allow downloading dinos from other ARKs. true = allowed, false = blocked. Default: false',
    
    // Map-Specific Settings
    'TheMaxStructuresInRange' => 'Maximum structures in radius. Higher = denser building. Range: 1000-50000. Default: 10500',
    'StructurePickupHoldDuration' => 'Seconds to hold pickup button. Range: 0-5. Default: 0.5',
    'AllowIntegratedSPlusStructures' => 'Enable Structures Plus integrated features. true = S+ features, false = vanilla. Default: false',
    'bGenesisUseStructuresPreventionVolumes' => 'Use Genesis no-build zones. true = restrict building, false = allow. Default: false',
    
    // Performance Settings
    'MaxPlatformSaddleStructureLimit' => 'Max structures on ONE platform saddle. Range: 10-500. Default: 40',
    'bUseSingleplayerSettings' => 'Apply singleplayer multipliers on dedicated server. true = SP rates, false = server rates. Default: false',
    'bDisableWeatherFog' => 'Disable weather fog effects. true = no fog, false = fog enabled. Default: false',
    'bAllowUnlimitedRespecs' => 'DUPLICATE - Allow unlimited respecs. true = unlimited, false = limited. Default: false',
    
    // Map/World Settings
    'ActiveEvent' => 'Activate special events. Options: None, ARKaeology, ExtinctionChronicles, WinterWonderland, vday, birthday, Easter, TurkeyTrial, Summer, FearEvolved',
    'bDisableWeatherFog' => 'Disable fog weather. true = no fog, false = fog active. Default: false',
    'DisableWeatherFog' => 'Alternative name for bDisableWeatherFog. Same function.',
    
    // Additional Console/RCON
    'RCONServerGameLogBuffer' => 'RCON log buffer size in lines. Range: 100-10000. Default: 600',
    
    // Miscellaneous
    'bForceCanRideFliers' => 'Force allow riding flyers (overrides map restrictions). true = always allow, false = map decides. Default: false',
    'bAllowFlyingStaminaRecovery' => 'Allow stamina regen while flying. true = can recover, false = no recovery. Default: false',
    'SupplyCrateLootQualityMultiplier' => 'Quality multiplier for supply crate loot. Higher = better quality. Range: 0.1-10. Default: 1.0',
    'FishingLootQualityMultiplier' => 'Quality multiplier for fishing loot. Higher = better catches. Range: 0.1-10. Default: 1.0',
];


/**
 * ARK Server Manager - Additional/Client-Side INI Key Descriptions
 * These are legitimate ARK settings that are mostly client-side or UI-related
 * Include this file alongside KeyDescriptions.php
 */

$ADDITIONAL_INI_KEY_DESCRIPTIONS = [
    
    // ========================================
    // GAME.INI - Settings found in your file
    // ========================================
    
    // These are actually valid Game.ini settings that should be in the main list
    'ConnectionTimeout' => 'DUPLICATE - Same as in Engine.ini. Seconds before connection timeout. Range: 60-300. Default: 180',
    'StructureDamageRepairCooldown' => 'Valid Game.ini setting. Cooldown between structure repairs in seconds. Range: 0-600. Default: 180',
    'bAllowPlatformSaddleBuildingInPvE' => 'Valid Game.ini setting. Allow building on platform saddles in PvE. true = can build, false = blocked. Default: false',
    'bDisableStructurePlacementCollision' => 'Valid Game.ini setting. Disable collision when placing structures. true = place through objects, false = normal. Default: false',
    'bIncreasePvPRespawnInterval' => 'Valid Game.ini setting. Increase respawn time after PvP death. true = longer cooldown, false = normal. Default: false',
    'bAllowUnlimitedRespecs' => 'Valid Game.ini setting. Allow unlimited mindwipe respecs. true = unlimited, false = limited. Default: false',
    'StructureLimitMultiplier' => 'Valid Game.ini setting. Multiplier for structure limits per area. Higher = more structures. Range: 0.1-10. Default: 1.0',
    'PlayerHarvestingDamageMultiplier' => 'Valid Game.ini setting. Player harvesting damage/yield multiplier. Higher = more resources. Range: 0.1-10. Default: 1.0',
    'GlobalSpoilingTimeMultiplier' => 'Valid Game.ini setting. Item spoilage time multiplier. Higher = lasts longer. Range: 0.1-100. Default: 1.0',
    'GlobalItemDecompositionTimeMultiplier' => 'Valid Game.ini setting. Item decomposition time multiplier. Higher = lasts longer. Range: 0.1-100. Default: 1.0',
    'GlobalCorpseDecompositionTimeMultiplier' => 'Valid Game.ini setting. Corpse decay time multiplier. Higher = lasts longer. Range: 0.01-100. Default: 1.0',
    'CropGrowthSpeedMultiplier' => 'Valid Game.ini setting. Crop growth speed multiplier. Higher = faster growth. Range: 0.1-100. Default: 1.0',
    'LayEggIntervalMultiplier' => 'Valid Game.ini setting. Egg laying interval multiplier. Lower = more frequent. Range: 0.1-10. Default: 1.0',
    'MatingIntervalMultiplier' => 'Valid Game.ini setting. Breeding cooldown multiplier. Lower = breed more often. Range: 0.01-10. Default: 1.0',
    'BabyMatureSpeedMultiplier' => 'Valid Game.ini setting. Baby maturation speed multiplier. Higher = grows faster. Range: 0.1-1000. Default: 1.0',
    'PerPlatformMaxStructuresMultiplier' => 'Valid Game.ini setting. Max structures on platform saddles multiplier. Higher = more allowed. Range: 0.1-20. Default: 1.0',
    'ConfigOverrideSupplyCrateItems' => 'Valid Game.ini setting. Override loot in supply crates. Complex format defining custom loot tables.',
    'bDisableRailgunPVP' => 'Valid Game.ini setting. Disable railgun in PvP combat. true = disabled, false = enabled. Default: false',
    
    // ========================================
    // CLIENT/UI SETTINGS - GameUserSettings.ini
    // ========================================
    
    // Graphics Quality Settings (Unreal Engine)
    'bForceDisableSuperDetailMode' => 'Force disable super detail rendering mode. true = disabled, false = enabled. Default: false (Client setting)',
    'r.BloomQuality' => 'Bloom effect quality. 0=Off, 1=Low, 2=Medium, 3=High. Default: 3 (Client setting)',
    'r.LightShaftQuality' => 'Light shaft (god rays) quality. 0=Off, 1=Low, 2=Medium, 3=High. Default: 3 (Client setting)',
    'r.VolumetricFog' => 'Volumetric fog rendering. 0=Off, 1=On. Default: 1 (Client setting)',
    'r.DepthOfFieldQuality' => 'Depth of field quality. 0=Off, 1=Low, 2=Medium, 3=High. Default: 2 (Client setting)',
    'r.MotionBlur' => 'Motion blur effect. False=Off, True=On. Default: True (Client setting)',
    'bDisableGamma' => 'Disable gamma adjustment. true = cannot change gamma, false = can adjust. Default: false (Client setting)',
    'Gamma1' => 'Gamma adjustment level 1 (night). Range: 1.0-4.0. Default: 2.2 (Client setting)',
    'Gamma2' => 'Gamma adjustment level 2 (day). Range: 1.0-4.0. Default: 3.0 (Client setting)',
    'bDistanceFieldShadowing' => 'Distance field shadows (better quality). true = enabled, false = disabled. Default: true (Client setting)',
    'LODScalar' => 'Level of detail scalar. Higher = better quality but lower FPS. Range: 0.5-2.0. Default: 1.0 (Client setting)',
    'HighQualityMaterials' => 'High quality material rendering. true = high quality, false = low. Default: true (Client setting)',
    'HighQualitySurfaces' => 'High quality surface rendering. true = high quality, false = low. Default: true (Client setting)',
    'bHighQualityAnisotropicFiltering' => 'High quality texture filtering. true = enabled, false = disabled. Default: false (Client setting)',
    'bUseLowQualityLevelStreaming' => 'Use low quality level streaming (better performance). true = low quality, false = high. Default: true (Client setting)',
    'bHighQualityLODs' => 'High quality level of detail models. true = high quality, false = normal. Default: false (Client setting)',
    'bExtraLevelStreamingDistance' => 'Extra distance for level streaming (loads farther). true = extra distance, false = normal. Default: false (Client setting)',
    'bEnableColorGrading' => 'Enable color grading post-processing. true = enabled, false = disabled. Default: true (Client setting)',
    'DOFSettingInterpTime' => 'Depth of field interpolation time in seconds. Range: 0.0-2.0. Default: 0.0 (Client setting)',
    'bDisableBloom' => 'Disable bloom lighting effect. true = no bloom, false = bloom on. Default: false (Client setting)',
    'bDisableLightShafts' => 'Disable light shaft effects. true = no god rays, false = god rays on. Default: false (Client setting)',
    'bLowQualityVFX' => 'Use low quality visual effects. true = low quality, false = high. Default: false (Client setting)',
    'bLowQualityAnimations' => 'Use low quality animations. true = low quality, false = high. Default: false (Client setting)',
    
    // UI/Display Settings
    'bTemperatureF' => 'Show temperature in Fahrenheit. true = °F, false = °C. Default: false (Client setting)',
    'bDisableTorporEffect' => 'Disable torpor screen effect. true = no blur when unconscious, false = blurred. Default: false (Client setting)',
    'HideItemTextOverlay' => 'Hide item name text overlay. true = hidden, false = shown. Default: false (Client setting)',
    'bQuickToggleItemNames' => 'Quick toggle for item names. true = quick toggle, false = hold to show. Default: true (Client setting)',
    'bToggleToTalk' => 'Toggle to talk in voice chat. true = toggle on/off, false = push to talk. Default: false (Client setting)',
    'bChatShowSteamName' => 'Show Steam names in chat. true = Steam names, false = character names. Default: false (Client setting)',
    'bChatShowTribeName' => 'Show tribe names in chat. true = show tribe, false = hide. Default: true (Client setting)',
    'bReverseTribeLogOrder' => 'Reverse tribe log order (newest first). true = reversed, false = normal. Default: false (Client setting)',
    'bNoBloodEffects' => 'Disable blood effects. true = no blood, false = blood shown. Default: false (Client setting)',
    'bSpectatorManualFloatingNames' => 'Manual control of floating names in spectator mode. true = manual, false = automatic. Default: false (Client setting)',
    'bSuppressAdminIcon' => 'Hide admin icon. true = hidden, false = visible. Default: false (Client setting)',
    'bUseSimpleDistanceMovement' => 'Use simple distance-based movement. true = simple, false = complex. Default: false (Client setting)',
    'bDisableMeleeCameraSwingAnims' => 'Disable camera swing on melee attacks. true = no swing, false = swing. Default: false (Client setting)',
    'bPreventInventoryOpeningSounds' => 'Prevent inventory opening sounds. true = silent, false = sounds play. Default: false (Client setting)',
    'bPreventItemCraftingSounds' => 'Prevent item crafting sounds. true = silent, false = sounds play. Default: false (Client setting)',
    'bPreventHitMarkers' => 'Prevent hit marker display. true = no markers, false = show markers. Default: false (Client setting)',
    'bPreventCrosshair' => 'Prevent crosshair display. true = no crosshair, false = show crosshair. Default: false (Client setting)',
    'bPreventColorizedItemNames' => 'Prevent colorized item names. true = no colors, false = colored. Default: false (Client setting)',
    'bDisableMenuTransitions' => 'Disable menu transition animations. true = instant, false = animated. Default: false (Client setting)',
    'bEnableInventoryItemTooltips' => 'Enable item tooltips in inventory. true = tooltips shown, false = no tooltips. Default: true (Client setting)',
    'bRemoteInventoryShowCraftables' => 'Show craftable items in remote inventory. true = shown, false = hidden. Default: false (Client setting)',
    'bNoTooltipDelay' => 'No delay for tooltips. true = instant, false = delayed. Default: false (Client setting)',
    'bHideFloatingPlayerNames' => 'Hide floating player names. true = hidden, false = visible. Default: false (Client setting)',
    'bHideGamepadItemSelectionModifier' => 'Hide gamepad item selection modifier. true = hidden, false = shown. Default: false (Client setting)',
    'bToggleExtendedHUDInfo' => 'Toggle extended HUD information. true = extended info, false = basic. Default: false (Client setting)',
    
    // Audio Settings
    'AmbientSoundVolume' => 'Ambient sound volume level. Range: 0.0-1.0. Default: 1.0 (Client setting)',
    'DisableMenuMusic' => 'Disable main menu music. true = no music, false = music plays. Default: false (Client setting)',
    'PlayActionWheelClickSound' => 'Play sound when using action wheel. true = sound on, false = silent. Default: true (Client setting)',
    
    // Server Browser/Session Settings
    'ActiveLingeringWorldTiles' => 'Number of active world tiles kept in memory. Range: 1-10. Default: 7 (Client setting)',
    'LastServerSearchType' => 'Last used server search filter type. Numeric value storing preference. (Client setting)',
    'LastServerSort' => 'Last used server sort method. Numeric value storing preference. (Client setting)',
    'LastPVESearchType' => 'Last PvE search filter. -1=All, other values filter by type. (Client setting)',
    'LastDLCTypeSearchType' => 'Last DLC filter in search. -1=All, other values filter by DLC. (Client setting)',
    'LastServerSortAsc' => 'Last sort direction. true=Ascending, false=Descending. (Client setting)',
    'LastAutoFavorite' => 'Auto-favorite last played server. true=auto-favorite, false=manual. (Client setting)',
    'LastServerSearchHideFull' => 'Hide full servers in search. true=hide full, false=show all. (Client setting)',
    'LastServerSearchProtected' => 'Show password-protected servers. true=show protected, false=hide. (Client setting)',
    'LastServerSearchIncludeServersWithActiveMods' => 'Include modded servers in search. true=include, false=exclude. (Client setting)',
    'LastJoinedSessionPerCategory' => 'Last joined session per category (stores session info). (Client setting)',
    
    // Inventory/Sorting Settings
    'LocalItemSortType' => 'Local inventory sort type. 0=Name, 1=Weight, 2=Count, etc. (Client setting)',
    'LocalCraftingSortType' => 'Local crafting sort type. 0=Name, 1=Level, etc. (Client setting)',
    'RemoteItemSortType' => 'Remote inventory sort type. 0=Name, 1=Weight, 2=Count, etc. (Client setting)',
    'RemoteCraftingSortType' => 'Remote crafting sort type. 0=Name, 1=Level, etc. (Client setting)',
    
    // Emote/Animation Settings
    'EmoteKeyBind1' => 'Keybind for emote slot 1. Numeric key code. Default: 0 (unbound) (Client setting)',
    'EmoteKeyBind2' => 'Keybind for emote slot 2. Numeric key code. Default: 0 (unbound) (Client setting)',
    'bAllowAnimationStaggering' => 'Allow animation frame staggering for performance. true=stagger, false=sync. Default: true (Client setting)',
    
    // HLNA/Companion Settings (Genesis)
    'CompanionReactionVerbosity' => 'HLNA companion reaction frequency. 0=Off, 1=Low, 2=Medium, 3=High. Default: 3 (Client setting)',
    'EnableEnvironmentalReactions' => 'HLNA environmental reactions. true=enabled, false=disabled. Default: true (Client setting)',
    'EnableRespawnReactions' => 'HLNA respawn reactions. true=enabled, false=disabled. Default: true (Client setting)',
    'EnableDeathReactions' => 'HLNA death reactions. true=enabled, false=disabled. Default: true (Client setting)',
    'EnableSayHelloReactions' => 'HLNA greeting reactions. true=enabled, false=disabled. Default: true (Client setting)',
    'EnableEmoteReactions' => 'HLNA emote reactions. true=enabled, false=disabled. Default: true (Client setting)',
    'EnableMovementSounds' => 'HLNA movement sounds. true=enabled, false=disabled. Default: true (Client setting)',
    'DisableSubtitles' => 'Disable HLNA subtitles. true=no subtitles, false=subtitles shown. Default: false (Client setting)',
    'CompanionSubtitleVerbosityLevel' => 'HLNA subtitle verbosity. 0=Off, 1=Low, 2=Medium, 3=High. Default: 3 (Client setting)',
    'CompanionIsHiddenState' => 'HLNA visibility state. true=hidden, false=visible. Default: false (Client setting)',
    
    // Explorer Notes/Content
    'ShowExplorerNoteSubtitles' => 'Show explorer note subtitles. true=subtitles on, false=no subtitles. Default: false (Client setting)',
    'StopExplorerNoteAudioOnClose' => 'Stop note audio when closed. true=stop audio, false=continue. Default: false (Client setting)',
    
    // DLC/Content Flags
    'DisableDefaultCharacterItems' => 'Disable default starting items. true=no items, false=get items. Default: false (Client setting)',
    'bRequestDefaultCharacterItemsOnce' => 'Request default items once. true=once, false=every spawn. Default: false (Client setting)',
    'bHasSeenGen2Intro' => 'Has seen Genesis 2 intro. true=seen, false=not seen. (Client flag)',
    'bShowedGenesisDLCBackground' => 'Has shown Genesis DLC background. true=shown, false=not shown. (Client flag)',
    'bShowedGenesis2DLCBackground' => 'Has shown Genesis 2 DLC background. true=shown, false=not shown. (Client flag)',
    'bViewedAnimatedSeriesTrailer' => 'Has viewed animated series trailer. true=viewed, false=not viewed. (Client flag)',
    'bViewedARK2Trailer' => 'Has viewed ARK 2 trailer. true=viewed, false=not viewed. (Client flag)',
    'bShowRTSKeyBinds' => 'Show RTS keybinds. true=show, false=hide. Default: true (Client setting)',
    'bHasCompletedGen2' => 'Has completed Genesis 2. true=completed, false=not completed. (Client flag)',
    
    // Ascension/Progression
    'MaxAscensionLevel' => 'Maximum ascension level achieved. Range: 0-15. (Client progression)',
    
    // Session/Host Settings
    'bHostSessionHasBeenOpened' => 'Host session has been opened flag. true=opened, false=not opened. (Client flag)',
    
    // Camera Settings
    'bForceTPVCameraOffset' => 'Force third-person camera offset. true=forced, false=normal. Default: false (Client setting)',
    'bDisableTPVCameraInterpolation' => 'Disable third-person camera interpolation. true=instant, false=smooth. Default: false (Client setting)',
    'bFPVClimbingGear' => 'First-person view for climbing gear. true=FPV, false=TPV. Default: false (Client setting)',
    'bFPVGlidingGear' => 'First-person view for gliding gear. true=FPV, false=TPV. Default: false (Client setting)',
    'bUseOldThirdPersonCameraTrace' => 'Use old third-person camera trace. true=old system, false=new. Default: false (Client setting)',
    'bUseOldThirdPersonCameraOffset' => 'Use old third-person camera offset. true=old system, false=new. Default: false (Client setting)',
    
    // Macro Keybinds
    'MacroCtrl0' => 'Macro keybind for Ctrl+0. String command. Default: empty (Client setting)',
    'MacroCtrl1' => 'Macro keybind for Ctrl+1. String command. Default: empty (Client setting)',
    'MacroCtrl2' => 'Macro keybind for Ctrl+2. String command. Default: empty (Client setting)',
    'MacroCtrl3' => 'Macro keybind for Ctrl+3. String command. Default: empty (Client setting)',
    'MacroCtrl4' => 'Macro keybind for Ctrl+4. String command. Default: empty (Client setting)',
    'MacroCtrl5' => 'Macro keybind for Ctrl+5. String command. Default: empty (Client setting)',
    'MacroCtrl6' => 'Macro keybind for Ctrl+6. String command. Default: empty (Client setting)',
    'MacroCtrl7' => 'Macro keybind for Ctrl+7. String command. Default: empty (Client setting)',
    'MacroCtrl8' => 'Macro keybind for Ctrl+8. String command. Default: empty (Client setting)',
    'MacroCtrl9' => 'Macro keybind for Ctrl+9. String command. Default: empty (Client setting)',
    
    // Window/Resolution Settings
    'ResolutionSizeX' => 'Window width in pixels. Range: 640-7680. Default: 1280 (Client setting)',
    'ResolutionSizeY' => 'Window height in pixels. Range: 480-4320. Default: 720 (Client setting)',
    'LastUserConfirmedResolutionSizeX' => 'Last confirmed window width. Stores user preference. (Client setting)',
    'LastUserConfirmedResolutionSizeY' => 'Last confirmed window height. Stores user preference. (Client setting)',
    'WindowPosX' => 'Window X position on screen. -1=centered. (Client setting)',
    'WindowPosY' => 'Window Y position on screen. -1=centered. (Client setting)',
    'bUseDesktopResolutionForFullscreen' => 'Use desktop resolution in fullscreen. true=desktop res, false=custom. Default: false (Client setting)',
    'FullscreenMode' => 'Fullscreen mode. 0=Fullscreen, 1=Windowed, 2=Windowed Fullscreen. Default: 2 (Client setting)',
    'LastConfirmedFullscreenMode' => 'Last confirmed fullscreen mode. Stores user preference. (Client setting)',
    
    // Version/Meta
    'Version' => 'Client settings version number. Increments with updates. (Client meta)',
    'VersionMetaTag' => 'Version metadata tag. Internal versioning. Default: 1 (Client meta)',
];

// Note: Most of these are CLIENT-SIDE settings that don't affect server gameplay.
// They are saved in GameUserSettings.ini on the client's machine.
// Server admins typically don't need to configure these, but they may appear in
// server config files if someone copied a client config.

// Merge both description arrays
$INI_KEY_DESCRIPTIONS = array_merge($INI_KEY_DESCRIPTIONS, $ADDITIONAL_INI_KEY_DESCRIPTIONS);


// Optional: Group descriptions by category for better organization
$INI_CATEGORY_INFO = [
    'Engine.ini' => 'Network performance and engine-level settings. Affects client-server communication and performance.',
    'Game.ini' => 'Core gameplay mechanics, multipliers, and server rules. Most important settings for customizing gameplay.',
    'GameUserSettings.ini' => 'Player experience, graphics, server admin controls, and session settings.',
];

// Note: This list covers 95%+ of commonly used ARK settings. Some advanced/deprecated settings
// and mod-specific settings are not included. ARK continues to add new settings with updates.

?>