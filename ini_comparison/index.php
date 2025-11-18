<?php
/**
 * ARK INI Settings Comparison Tool
 * Compares your current INI files against all available settings
 * Shows what you can add to each file
 */
define('ARK_MANAGER', true);
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../includes/keydescriptions.php';

// Define which settings belong in which INI file
$SETTINGS_BY_FILE = [
    'Engine.ini' => [
        // [/Script/Engine.GameNetworkManager]
        'MaxClientSmoothingDelta',
        'NetClientTicksPerSecond',
        'ClientNetSendMoveDeltaTime',
        'ClientNetSendMoveThrottleTime',
        
        // [/Script/OnlineSubsystemUtils.IpNetDriver] (Engine version)
        'MaxClientRate',
        'MaxInternetClientRate',
        'InitialConnectTimeout',
        'ConnectionTimeout',
        'LanServerMaxTickRate',
        
        // [/Script/Engine.Player]
        'ConfiguredInternetSpeed',
        'ConfiguredLanSpeed',
    ],
    
    'Game.ini' => [
        // [/Script/OnlineSubsystemUtils.IpNetDriver] (Game version)
        'NetServerMaxTickRate',
        'MaxNetTickRate',
        'MaxClientFrameRate',
        'KeepAliveTime',
        'RelevantTimeout',
        'SpawnPrioritySeconds',
        'ServerTravelPause',
        'bUseSeamlessTravel',
        'MaxChannelSize',
        'MaxPacketSize',
        'MaxPacketOverhead',
        'ReliableDataOverflowDelay',
        'NetConnectionTimeout',
        'AckTimeout',
        'TimeoutMultiplierForUnoptimizedBuilds',
        
        // [/script/shootergame.shootergamemode]
        'OverrideCaveTamingRestrictions',
        'DinoClassNameToAllowTameInCaves',
        'bAllowPlatformSaddleBuildingInPvE',
        'bAllowFlyerPlatformBuilding',
        'bFlyerPlatformAllowUnalignedDinoBasing',
        'PerPlatformMaxStructuresMultiplier',
        'MaxActiveWildDinos',
        'PerPlatformMaxActiveDinos',
        'ClampItemStats',
        'PreventOfflinePvPInterval',
        'bAllowUnlimitedRespecs',
        'bDisableStructureDecayPvE',
        'bPvEDisableFriendlyFire',
        'bUseCorpseLocator',
        'bShowCreativeMode',
        'bDisableImprintDinoBuff',
        'bAllowSpeedLeveling',
        'bAllowFlyerSpeedLeveling',
        'bDisableFlyerCarryPvE',
        'bDisableStructurePlacementCollision',
        'bAllowPlatformSaddleMultiFloors',
        'OverrideMaxExperiencePointsPlayer',
        'OverrideMaxExperiencePointsDino',
        'OverrideMaxExperiencePointsGeneric',
        'PlayerCharacterStaminaDrainMultiplier',
        'PlayerCharacterHealthRecoveryMultiplier',
        'DinoCountMultiplier',
        'TamedDinoLimit',
        'StructureLimitMultiplier',
        'MaxTribeLogs',
        'bDisableFriendlyFire',
        'bDisableLootCrates',
        'bIncreasePvPRespawnInterval',
        'bAutoPvETimer',
        'MaxNumberOfPlayersInTribe',
        'MaxAlliancesPerTribe',
        'MaxTribesPerAlliance',
        'bPvEAllowTribeWar',
        'bPvEAllowTribeWarCancel',
        'bAllowCustomRecipes',
        'GlobalPoweredBatteryDurabilityDecreasePerSecond',
        'bDisableGenesisMissions',
        'bDisableDefaultMapItemSets',
        'bDisableWorldBuffs',
        'bEnableWorldBuffScaling',
        'AdjustableMutagenSpawnDelayMultiplier',
        'BaseHexagonRewardMultiplier',
        'HexagonCostMultiplier',
        'bUseDinoLevelUpAnimations',
        'bAllowUnclaimDinos',
        'StructureDamageRepairCooldown',
        'PvPZoneStructureDamageMultiplier',
        'bPassiveDefensesDamageRiderlessDinos',
        'bLimitTurretsInRange',
        'bHardLimitTurretsInRange',
        'bIgnoreStructuresPreventionVolumes',
        'bGenesisUseStructuresPreventionVolumes',
        'DinoHarvestingDamageMultiplier',
        'PlayerHarvestingDamageMultiplier',
        'GlobalSpoilingTimeMultiplier',
        'GlobalItemDecompositionTimeMultiplier',
        'GlobalCorpseDecompositionTimeMultiplier',
        'CropGrowthSpeedMultiplier',
        'LayEggIntervalMultiplier',
        'MatingIntervalMultiplier',
        'EggHatchSpeedMultiplier',
        'BabyMatureSpeedMultiplier',
        'BabyCuddleIntervalMultiplier',
        'BabyImprintAmountMultiplier',
        'BabyCuddleGracePeriodMultiplier',
        'BabyCuddleLoseImprintQualitySpeedMultiplier',
        'UseCorpseLifeSpanMultiplier',
        'PreventCorpseDespawn',
        'HarvestHealthMultiplier',
        'HarvestAmountMultiplier',
        'DeathBagDecayTime',
        'bDisableRailgunPVP',
        'CustomRecipeEffectivenessMultiplier',
        'CustomRecipeSkillMultiplier',
        'MatingSpeedMultiplier',
        'BabyImprintingStatScaleMultiplier',
        'WildDinoCharacterFoodDrainMultiplier',
        'TamedDinoCharacterFoodDrainMultiplier',
        'StructureDamageMultiplier',
        'StructureResistanceMultiplier',
        'PlayerDamageMultiplier',
        'PlayerResistanceMultiplier',
        'DinoDamageMultiplier',
        'DinoResistanceMultiplier',
        'DinoTurretDamageMultiplier',
        'KillXPMultiplier',
        'HarvestXPMultiplier',
        'MutagenLevelBoost',
        'MutagenLevelBoost_Bred',
        'FastDecayInterval',
        'bPvPDisableFriendlyFire',
        'bDisableDinoRiding',
        'bDisableDinoTaming',
        'bDisableDinoBreeding',
        'MaxStructuresOnSaddle',
        'bAutoDestroyOldStructures',
        'bAutoDestroyStructures',
        'FuelConsumptionIntervalMultiplier',
        'bInfiniteStats',
        'bImmortalWorld',
        
        // Stat Multipliers (Player)
        'PerLevelStatsMultiplier_Player[0]',
        'PerLevelStatsMultiplier_Player[1]',
        'PerLevelStatsMultiplier_Player[2]',
        'PerLevelStatsMultiplier_Player[3]',
        'PerLevelStatsMultiplier_Player[4]',
        'PerLevelStatsMultiplier_Player[5]',
        'PerLevelStatsMultiplier_Player[6]',
        'PerLevelStatsMultiplier_Player[7]',
        'PerLevelStatsMultiplier_Player[8]',
        'PerLevelStatsMultiplier_Player[9]',
        'PerLevelStatsMultiplier_Player[10]',
        'PerLevelStatsMultiplier_Player[11]',
        
        // Stat Multipliers (Tamed Dino)
        'PerLevelStatsMultiplier_DinoTamed[0]',
        'PerLevelStatsMultiplier_DinoTamed[1]',
        'PerLevelStatsMultiplier_DinoTamed[2]',
        'PerLevelStatsMultiplier_DinoTamed[3]',
        'PerLevelStatsMultiplier_DinoTamed[4]',
        'PerLevelStatsMultiplier_DinoTamed[7]',
        'PerLevelStatsMultiplier_DinoTamed[8]',
        'PerLevelStatsMultiplier_DinoTamed[9]',
        'PerLevelStatsMultiplier_DinoTamed_Add[10]',
        
        // Stat Multipliers (Wild Dino)
        'PerLevelStatsMultiplier_DinoWild[0]',
        'PerLevelStatsMultiplier_DinoWild[1]',
        'PerLevelStatsMultiplier_DinoWild[2]',
        'PerLevelStatsMultiplier_DinoWild[3]',
        'PerLevelStatsMultiplier_DinoWild[4]',
        'PerLevelStatsMultiplier_DinoWild[7]',
        'PerLevelStatsMultiplier_DinoWild[8]',
        'PerLevelStatsMultiplier_DinoWild[9]',
        
        // Advanced Settings
        'OverridePlayerLevelEngramPoints',
        'OverrideEngramEntries',
        'OverrideNamedEngramEntries',
        'EngramEntryAutoUnlocks',
        'ExperiencePointsForLevel',
        'LevelExperienceRampOverrides',
        'ConfigAddNPCSpawnEntriesContainer',
        'ConfigSubtractNPCSpawnEntriesContainer',
        'ConfigOverrideNPCSpawnEntriesContainer',
        'NPCReplacements',
        'DinoSpawnWeightMultipliers',
        'PreventDinoTameClassNames',
        'ConfigOverrideSupplyCrateItems',
        'ConfigAddSupplyCrateItems',
        'ConfigSubtractSupplyCrateItems',
        'ConfigOverrideCraftingCostItems',
        'ConfigOverrideItemCraftingCosts',
        'HarvestResourceItemAmountClassMultipliers',
        'ConfigOverrideItemMaxQuantity',
        'ConfigOverrideItemWeight',
    ],
    
    'GameUserSettings.ini' => [
        // [ServerSettings]
        'OverrideStructurePlatformPrevention',
        'AutoSavePeriodMinutes',
        'MaxClientSendsPerTick',
        'KickIdlePlayersPeriod',
        'ServerHardMaxNumPlayers',
        'ClampResourceHarvestDamage',
        'MaxPingLimit',
        'NetworkIdleTimeout',
        'bRawSockets',
        'AllowedOrigins',
        'bUseBattlEye',
        'bServerAllowAnsel',
        'ServerAutoForceRespawnWildDinosInterval',
        'PvEDinoTurretDamageMultiplier',
        'MaxTamedDinos',
        'ListenServerTetherDistanceMultiplier',
        'ShowDebug',
        'XPMultiplier',
        'CraftXPMultiplier',
        'GenericXPMultiplier',
        'SpecialXPMultiplier',
        'AllowTekSuitPowersInGenesis',
        'PreventDownloadSurvivors',
        'PreventDownloadItems',
        'PreventDownloadDinos',
        'PreventUploadSurvivors',
        'PreventUploadItems',
        'PreventUploadDinos',
        'NoTributeDownloads',
        'TributeItemExpirationSeconds',
        'TributeDinoExpirationSeconds',
        'TributeCharacterExpirationSeconds',
        'ShowMapPlayerLocation',
        'AllowThirdPersonPlayer',
        'ServerCrosshair',
        'TheMaxStructuresInRange',
        'StructurePreventResourceRadiusMultiplier',
        'TribeNameChangeCooldown',
        'PlatformSaddleBuildAreaBoundsMultiplier',
        'StructurePickupTimeAfterPlacement',
        'StructurePickupHoldDuration',
        'AllowIntegratedSPlusStructures',
        'AllowHideDamageSourceFromLogs',
        'MaxNumOfSaveBackups',
        'RCONServerGameLogBuffer',
        'AllowHitMarkers',
        'AllowCrateSpawnsOnTopOfStructures',
        'GreaterRiftActivationMultiplier',
        'ShowAnniversaryContent',
        'ServerPassword',
        'ServerAdminPassword',
        'SpectatorPassword',
        'RCONEnabled',
        'RCONPort',
        'AdminLogging',
        'ServerLogging',
        'bShowAdminCommands',
        'ActiveMods',
        'TribeLogDestroyedEnemyStructures',
        'bFilterTribeNames',
        'bFilterCharacterNames',
        'bFilterChat',
        'AllowSharedConnections',
        'ServerHardcore',
        'ServerPVE',
        'AllowCaveBuildingPvE',
        'AllowCaveBuildingPvP',
        'EnableExtraStructurePreventionVolumes',
        'CrossARKAllowForeignDinoDownloads',
        'PreventOfflinePvP',
        'PreventTribeAlliances',
        'PreventDiseases',
        'NonPermanentDiseases',
        'PreventSpawnAnimations',
        'MaxGateFrameOnSaddles',
        'RandomSupplyCratePoints',
        'MaxHexagonsPerCharacter',
        'UseFjordurTraversalBuff',
        'globalVoiceChat',
        'proximityChat',
        'alwaysNotifyPlayerLeft',
        'DontAlwaysNotifyPlayerJoined',
        'ServerForceNoHud',
        'EnablePVPGamma',
        'DisablePvEGamma',
        'ShowFloatingDamageText',
        'AllowFlyerCarryPVE',
        'DinoCharacterFoodDrainMultiplier',
        'AllowRaidDinoFeeding',
        'DisableDinoDecayPvE',
        'PvPDinoDecay',
        'AutoDestroyDecayedDinos',
        'MaxPersonalTamedDinos',
        'PersonalTamedDinosSaddleStructureCost',
        'DisableImprintDinoBuff',
        'AllowAnyoneBabyImprintCuddle',
        'TamingSpeedMultiplier',
        'TamingAffinitySpeedMultiplier',
        'ResourcesRespawnPeriodMultiplier',
        'UseOptimizedHarvestingHealth',
        'ClampItemSpoilingTimes',
        'DayCycleSpeedScale',
        'DayTimeSpeedScale',
        'NightTimeSpeedScale',
        'PvPStructureDecay',
        'DisableStructureDecayPVE',
        'ForceAllStructureLocking',
        'OnlyAutoDestroyCoreStructures',
        'OnlyDecayUnsnappedCoreStructures',
        'FastDecayUnsnappedCoreStructures',
        'DestroyUnconnectedWaterPipes',
        'IgnoreLimitMaxStructuresInRangeTypeFlag',
        'AlwaysAllowStructurePickup',
        'PlayerCharacterFoodDrainMultiplier',
        'PlayerCharacterWaterDrainMultiplier',
        'bUseVSync',
        'bUseDynamicConfig',
        'DisableStructurePlacementCollision',
        'AllowCropPlotStacking',
        'DifficultyOffset',
        'bOverrideOfficialDifficulty',
        'OverrideOfficialDifficulty',
        'EnableCryopodNerf',
        'EnableCryosicknessPVE',
        'CryopodNerfDuration',
        'CryopodNerfDamageMult',
        'CryopodNerfIncomingDamageMultPercent',
        'DisableCryopodEnemyCheck',
        'AllowCryoFridgeOnSaddle',
        'DisableCryopodFridgeRequirement',
        'OxygenSwimSpeedStatMultiplier',
        'RaidDinoCharacterFoodDrainMultiplier',
        'PvEDinoDecayPeriodMultiplier',
        'ItemStackSizeMultiplier',
        'BabyFoodConsumptionSpeedMultiplier',
        'ForceAllowCaveFlyers',
        'AllowUnlimitedRespec',
        'bAutoUnlockAllEngrams',
        'bShowFloatingDamageText',
        'bCrossARKAllowForeignDinoDownloads',
        'MaxPlatformSaddleStructureLimit',
        'bUseSingleplayerSettings',
        'bDisableWeatherFog',
        'DisableWeatherFog',
        'ActiveEvent',
        'bServerUseDynamicConfig',
        'QueryPort',
        'Port',
        'MinimumDinoReuploadInterval',
        'bClampItemStats',
        'bForceCanRideFliers',
        'bAllowFlyingStaminaRecovery',
        'SupplyCrateLootQualityMultiplier',
        'FishingLootQualityMultiplier',
        'TribeSlotReuseCooldown',
        
        // [/Script/ShooterGame.ShooterGameUserSettings]
        'MasterAudioVolume',
        'MusicAudioVolume',
        'SFXAudioVolume',
        'VoiceAudioVolume',
        'CharacterAudioVolume',
        'UIScaling',
        'UIQuickbarScaling',
        'CameraShakeScale',
        'bFirstPersonRiding',
        'bThirdPersonPlayer',
        'bInventoryHideUnlearnedEngrams',
        'bShowStatusNotificationMessages',
        'TrueSkyQuality',
        'FOVMultiplier',
        'GroundClutterDensity',
        'bFilmGrain',
        'bMotionBlur',
        'bUseDistanceFieldAmbientOcclusion',
        'bUseSSAO',
        'bShowChatBox',
        'bCameraViewBob',
        'bInvertLookY',
        'bFloatingNames',
        'bChatBubbles',
        'bHideServerInfo',
        'bJoinNotifications',
        'bCraftablesShowAllItems',
        'bLocalInventoryItemsShowAllItems',
        'bLocalInventoryCraftingShowAllItems',
        'bRemoteInventoryItemsShowAllItems',
        'bRemoteInventoryCraftingShowAllItems',
        'bRemoteInventoryShowEngrams',
        'LookLeftRightSensitivity',
        'LookUpDownSensitivity',
        'GraphicsQuality',
        'ClientNetQuality',
        
        // [ScalabilityGroups]
        'sg.ResolutionQuality',
        'sg.ViewDistanceQuality',
        'sg.AntiAliasingQuality',
        'sg.ShadowQuality',
        'sg.PostProcessQuality',
        'sg.TextureQuality',
        'sg.EffectsQuality',
        'sg.TrueSkyQuality',
        'sg.GroundClutterQuality',
        'sg.IBLQuality',
        'sg.HeightFieldShadowQuality',
        'sg.GroundClutterRadius',
        
        // [StructuresPlus]
        'RespectConfigOverrideItemMaxQuantity',
        'CompostBinCraftingSpeedMultiplier',
        'BeerBarrelCraftingSpeedMultiplier',
        'AllowPersonalOwnership',
        'LockStructures',
        
        // [/Script/Engine.GameSession]
        'MaxPlayers',
        
        // [MultiHome]
        'MultiHome',
        
        // [MessageOfTheDay]
        'Message',
        'Duration',
        
        // [DinoTracker]
        'DisableWildTracking',
        'DisableWildFindButton',
        'DisableTribememberTracking',
        'DisableDeathTracking',
        'DisableAllyTracking',
        'DisableWildCoordinates',
        
        // [/Game/PrimalEarth/CoreBlueprints/TestGameMode.TestGameMode_C]
        'bServerGameLogEnabled',
        
        // [SessionSettings]
        'SessionName',
    ],
];

/**
 * Parse an INI file and extract all keys
 */
function parseIniKeys($filepath) {
    if (!file_exists($filepath)) {
        return [];
    }
    
    $content = file_get_contents($filepath);
    $keys = [];
    
    // Match all key=value pairs
    preg_match_all('/^([a-zA-Z_][a-zA-Z0-9_\[\]\.]*)\s*=/m', $content, $matches);
    
    if (!empty($matches[1])) {
        $keys = array_unique($matches[1]);
    }
    
    return $keys;
}

/**
 * Generate comparison report
 */
function generateComparisonReport($INI_FILES) {
    global $SETTINGS_BY_FILE, $INI_KEY_DESCRIPTIONS;
    
    $report = [];
    
    foreach ($INI_FILES as $iniName => $filepath) {
        $currentKeys = parseIniKeys($filepath);
        $availableKeys = $SETTINGS_BY_FILE[$iniName] ?? [];
        
        // Find missing keys
        $missingKeys = array_diff($availableKeys, $currentKeys);
        
        // Find unknown keys (in your file but not in our list)
        $unknownKeys = array_diff($currentKeys, $availableKeys);
        
        $report[$iniName] = [
            'current_count' => count($currentKeys),
            'available_count' => count($availableKeys),
            'missing_count' => count($missingKeys),
            'unknown_count' => count($unknownKeys),
            'missing_keys' => $missingKeys,
            'unknown_keys' => $unknownKeys,
            'current_keys' => $currentKeys,
        ];
    }
    
    return $report;
}

$pageTitle = 'INI Comparison';
include '../includes/header.php';

/**
 * Display comparison report in HTML
 */
function displayComparisonReport($report) {
    global $INI_KEY_DESCRIPTIONS;
    
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #1a1a2e; color: #e0e0e0; }
        .ini-section { background: #0f3460; border-radius: 10px; padding: 20px; margin-bottom: 30px; border: 2px solid #4CAF50; }
        .ini-section h2 { color: #4CAF50; margin-top: 0; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .stat-box { background: #16213e; padding: 15px; border-radius: 8px; flex: 1; min-width: 150px; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #4CAF50; }
        .stat-label { font-size: 0.9rem; color: #b0b0b0; }
        .key-list { background: #16213e; padding: 15px; border-radius: 8px; margin-top: 15px; max-height: 400px; overflow-y: auto; }
        .key-item { padding: 10px; border-bottom: 1px solid #2a2a3e; }
        .key-item:last-child { border-bottom: none; }
        .key-name { color: #64b5f6; font-weight: bold; font-family: monospace; }
        .key-desc { color: #b0b0b0; font-size: 0.9rem; margin-top: 5px; }
        .missing { color: #f44336; }
        .unknown { color: #ff9800; }
        h3 { color: #64b5f6; }
        .copy-btn { background: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-left: 10px; }
        .copy-btn:hover { background: #45a049; }
    </style>";
    
    foreach ($report as $iniName => $data) {
        echo "<div class='ini-section'>";
        echo "<h2>$iniName</h2>";
        
        echo "<div class='stats'>";
        echo "<div class='stat-box'>";
        echo "<div class='stat-number'>{$data['current_count']}</div>";
        echo "<div class='stat-label'>Current Settings</div>";
        echo "</div>";
        
        echo "<div class='stat-box'>";
        echo "<div class='stat-number'>{$data['available_count']}</div>";
        echo "<div class='stat-label'>Available Settings</div>";
        echo "</div>";
        
        echo "<div class='stat-box missing'>";
        echo "<div class='stat-number'>{$data['missing_count']}</div>";
        echo "<div class='stat-label'>Missing Settings</div>";
        echo "</div>";
        
        echo "<div class='stat-box unknown'>";
        echo "<div class='stat-number'>{$data['unknown_count']}</div>";
        echo "<div class='stat-label'>Unknown Settings</div>";
        echo "</div>";
        echo "</div>";
        
        // Missing settings
        if (!empty($data['missing_keys'])) {
            echo "<h3 class='missing'>⚠️ Missing Settings You Can Add ({$data['missing_count']})</h3>";
            echo "<div class='key-list'>";
            foreach ($data['missing_keys'] as $key) {
                $desc = $INI_KEY_DESCRIPTIONS[$key] ?? 'No description available';
                echo "<div class='key-item'>";
                echo "<div class='key-name'>$key</div>";
                echo "<div class='key-desc'>$desc</div>";
                echo "</div>";
            }
            echo "</div>";
        }
        
        // Unknown settings
        if (!empty($data['unknown_keys'])) {
            echo "<h3 class='unknown'>🔍 Unknown/Custom Settings in Your File ({$data['unknown_count']})</h3>";
            echo "<div class='key-list'>";
            foreach ($data['unknown_keys'] as $key) {
				$desc = $INI_KEY_DESCRIPTIONS[$key] ?? 'This setting is in your file but not in our standard list. It might be custom, mod-specific, or deprecated.';
                echo "<div class='key-item'>";
                echo "<div class='key-name'>$key</div>";
                echo "<div class='key-desc'>$desc</div>";
                echo "</div>";
            }
            echo "</div>";
        }
        
        echo "</div>";
    }
}

// Generate and display report
$report = generateComparisonReport($INI_FILES);
displayComparisonReport($report);

include '../includes/footer.php';

?>

