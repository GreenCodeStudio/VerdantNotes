if($args[0] -eq "--cd"){
    cd $PSScriptRoot
    $args = $args[1..($args.Length-1)]
}
if (!(test-path "modules/Core/script.ps1"))
{
    if (!(test-path "modules"))
    {
        mkdir "modules"
    }
    if (!(test-path "modules/Core"))
    {
        mkdir "modules/Core"
    }
    git submodule add https://github.com/GreenCodeStudio/GreenCodeFramework-Core.git "./modules/Core"
}
. ./modules/Core/script.ps1 $args
