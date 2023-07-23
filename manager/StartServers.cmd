@echo off
color 0B

setlocal enabledelayedexpansion

TITLE SharedData for PocketMine-PMMP Minecraft: Bedrock Edition
cd /d %~dp0

set "PATHS=C:\Users\Nam\Desktop\PMMP1,C:\Users\Nam\Desktop\PMMP2"

where /q powershell
if %errorlevel% equ 0 (
	echo PowerShell is installed on this machine.
) else (
	echo PowerShell is not installed on this machine.
	pause
	exit 1
)

echo .##.....##..######..##.....##....###....########..########.########..########.....###....########....###...
echo .##.....##.##....##.##.....##...##.##...##.....##.##.......##.....##.##.....##...##.##......##......##.##..
echo .##.....##.##.......##.....##..##...##..##.....##.##.......##.....##.##.....##..##...##.....##.....##...##.
echo .##.....##..######..#########.##.....##.########..######...##.....##.##.....##.##.....##....##....##.....##
echo ..##...##........##.##.....##.#########.##...##...##.......##.....##.##.....##.#########....##....#########
echo ...##.##...##....##.##.....##.##.....##.##....##..##.......##.....##.##.....##.##.....##....##....##.....##
echo ....###.....######..##.....##.##.....##.##.....##.########.########..########..##.....##....##....##.....##                                                                                 

echo ---------------STARTING-----------------
echo.

for %%a in (%PATHS%) do (

	if exist "%%a\bin\php\php.exe" (

		set CHECKED=0
		set STILL_RUNNING=0

		for /f "tokens=*" %%b in ('powershell -Command "Get-Process php | ForEach-Object Path"') do (
			if "%%b" == "%%a\bin\php\php.exe" (
				if !CHECKED! == 0 (
					set STILL_RUNNING=1
					set CHECKED=1
				)
			)
		)

		if !STILL_RUNNING! == 0 (
			if exist %%a\PocketMine-MP.phar (
				set POCKETMINE_FILE=%%a\PocketMine-MP.phar
			) else (
				copy PocketMine-MP.phar %%a\PocketMine-MP.phar >nul
				set POCKETMINE_FILE=%%a\PocketMine-MP.phar
			)

			cd %%a

			start "" %%a\start.cmd

			echo Started %%a

			cd /d "%~dp0"
		)
	)
)

echo.
echo ---------------STARTED-----------------

timeout /t 1 >nul