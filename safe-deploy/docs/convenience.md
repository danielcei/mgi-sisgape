
### Setting up convenient aliases

For convenience, you can create aliases to avoid typing long commands repeatedly:

#### For Mac/Linux users:

```bash
# Add these to your ~/.bashrc, ~/.zshrc, or equivalent shell configuration file
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
alias art='php artisan'
```

After adding the aliases, reload your shell configuration:

```bash
source ~/.bashrc  # or source ~/.zshrc if using Zsh
```

#### For Windows users:

Option 1: Using PowerShell profile (recommended):

1. First, check if you have a PowerShell profile:
   ```powershell
   Test-Path $PROFILE
   ```

2. If it returns `False`, create one:
   ```powershell
   New-Item -Path $PROFILE -Type File -Force
   ```

3. Open the profile in a text editor:
   ```powershell
   notepad $PROFILE
   ```

4. Add these functions to your profile:
   ```powershell
   function sail {
       if (Test-Path sail) {
           ./sail $args
       } else {
           ./vendor/bin/sail $args
       }
   }
   
   function art {
       php artisan $args
   }
   ```

5. Save and reload your profile:
   ```powershell
   . $PROFILE
   ```

Option 2: Create batch files in a directory that's in your PATH:

1. Create a file named `sail.bat` with the following content:
   ```batch
   @echo off
   if exist "sail" (
       sail %*
   ) else (
       .\vendor\bin\sail %*
   )
   ```

2. Create a file named `art.bat` with the following content:
   ```batch
   @echo off
   php artisan %*
   ```

3. Save these files in a directory that's in your PATH (e.g., `C:\Windows` or a custom bin directory)
