@echo off
color 0B

setlocal enabledelayedexpansion

TITLE SharedData for PocketMine-PMMP Minecraft: Bedrock Edition
cd /d %~dp0

REM You should setup here
set "PATHS=C:\Users\Nam\Desktop\PMMP1,C:\Users\Nam\Desktop\PMMP2"

where /q powershell
if %errorlevel% equ 0 (
	echo PowerShell is installed on this machine.
) else (
	echo PowerShell is not installed on this machine.
	pause
	exit 1
)

:LOOP

echo .##.....##..######..##.....##....###....########..########.########..########.....###....########....###...
echo .##.....##.##....##.##.....##...##.##...##.....##.##.......##.....##.##.....##...##.##......##......##.##..
echo .##.....##.##.......##.....##..##...##..##.....##.##.......##.....##.##.....##..##...##.....##.....##...##.
echo .##.....##..######..#########.##.....##.########..######...##.....##.##.....##.##.....##....##....##.....##
echo ..##...##........##.##.....##.#########.##...##...##.......##.....##.##.....##.#########....##....#########
echo ...##.##...##....##.##.....##.##.....##.##....##..##.......##.....##.##.....##.##.....##....##....##.....##
echo ....###.....######..##.....##.##.....##.##.....##.########.########..########..##.....##....##....##.....##                                                                                 

echo ---------------CHECKING-----------------
echo.

for %%a in (%PATHS%) do (

	title SharedData: Checking... for %%a
	timeout /t 1 >nul

	if exist "%%a\bin\php\php.exe" (

		set CHECKED=0
		set STILL_RUNNING=0

		for /f "tokens=*" %%b in ('powershell -Command "Get-Process php | ForEach-Object Path"') do (
			if "%%b" == "%%a\bin\php\php.exe" (
				if !CHECKED! == 0 (
					echo %%a 		is running...
					set STILL_RUNNING=1
					set CHECKED=1
				)
			)
		)

		if !STILL_RUNNING! == 0 (
			echo %%a 		is terminated.
			
			if exist %%a\PocketMine-MP.phar (
				del %%a\PocketMine-MP.phar
			)
		)
	)
)

timeout /t 1 >nul

echo.
echo --------------END CHECKING--------------

timeout /t 1 >nul

cls

goto :LOOP
