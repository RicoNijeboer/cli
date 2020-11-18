# CLI

<img src="/docs/assets/logo.png?raw=true" width="100" width="100" alt="CLI Logo" />

## Table of contents
- [CLI](#cli)
  - [Install guide](#install-guide)
  - [Commands](#commands)
    - [Install](#install)
    - [Update](#update)
    - [SCSS Variables](#scss-variables)
      - [Usage](#usage)
      - [Options](#options)
        - [-d or --dir](#-d-or---dir)
        - [-o or --out](#-o-or---out)
        - [-r or --rule](#-r-or---rule)
    - [Personal commands](#personal-commands)

## Install guide
Installing the command is really simple;
1. ```shell script
   git clone {repo}
   cd {repo-dir}
   composer install
   ```
1. Run the _**[Install](#install)**_ command
1. Go to any other directory
1. ```shell script
   rico {command}
   ```
1. Profit 🎉
1. _Optional:_ add an alias for the command (for example your own name 👍🏼)
   ```shell script
   ln -s ./rico ./bin/{alias}
   ```

## Commands

### Install
To run the CLI anywhere first you need to know the source of your terminal.
- For people using **ZSH** this will be `~/.zshrc`
- For people using **BASH** this will be `~/.bashrc`

When you know this path you can run the command below, replacing the `{SOURCE}` with your path.
 
```shell script
## Inside the cli directory
./rico install {SOURCE}
```

### Update
```shell script
rico update
```
Does what you think it would do; update this CLI tool to the newest version.

### SCSS Variables

#### Usage
Converts your SCSS to contain variables for easy theme switching 🥷🏼

The command can be run as shown below, which will, after generating the variables, copy your existing files to a new file with `__soft__` so you can check the result before "committing" to it 💍
(`base.scss` will be copied to `base.__soft__.scss` where the variables will be shown).

While checking, you can edit the `__soft__` files to your liking after which you can confirm the commit by telling `yes` to the command. Which will replace your original files with the corresponding `__soft__` file.

Either when the files are still `__soft__` files or when you have commited the changes, make sure you `_generated-variables.scss` is loaded when parsing the new file. Otherwise your builds will fail.

```shell script
## Create variables for these css rules ("background", "color", "border-bottom", "opacity"),
##        and put them in "_generated-variables.scss"
rico scss:variables -o _generated-variables.scss -r background -r color -r border-bottom -r opacity
```

```scss
/* Step 0 : base.scss */
.container {
    display    : flex;
    background : #fafafa;
    color      : black;

    .nav {
        .nav-item {
            opacity       : .5;
            color         : black;
            border-bottom : 2px solid rgba(255, 140, 0);
            
            &:hover {
                color : hsl(0, 100%, 50%);
            }
        }
    }
}

/* Step 1.1 :" _generated-variables.scss" is created by the command */
$container-background : #fafafa;
$container-color : black;
$container-nav-nav-item-opacity : .5;
$container-nav-nav-item-color : black;
$container-nav-nav-item-border-bottom : 2px solid rgba(255, 140, 0);
$container-nav-nav-item-hover-color : hsl(0, 100%, 50%);

/* Step 1.2 : "base.__soft__.scss" is created by the command */
.container {
    display    : flex;
    background : $container-background;
    color      : $container-color;

    .nav {
        .nav-item {
            opacity       : $container-nav-nav-item-opacity;
            color         : $container-nav-nav-item-color;
            border-bottom : $container-nav-nav-item-border-bottom;
            
            &:hover {
                color : $container-nav-nav-item-hover-color;
            }
        }
    }
}

/* Step 2.1 : If you want you can make changes to the "_generated-variables.scss" */
$container-background                : #fafafa;
$container-color                     : black;
$container-nav-nav-item-opacity      : .5;
$container-nav-nav-item-color        : black;
$container-nav-nav-item-border-color : rgba(255, 140, 0);
$container-nav-nav-item-hover-color  : hsl(0, 100%, 50%);

/* Step 2.2 : If you want you can make changes to the "base.__soft__.scss" */
.container {
    display    : flex;
    background : $container-background;
    color      : $container-color;

    .nav {
        .nav-item {
            opacity       : $container-nav-nav-item-opacity;
            color         : $container-nav-nav-item-color;
            border-bottom : 2px solid $container-nav-nav-item-border-color;
            
            &:hover {
                color : $container-nav-nav-item-hover-color;
            }
        }
    }
}

/* Step 3 : Tell the command you want to commit to the soft files */

/* Step 4 : Profit 🎉 */
```

#### Options
The `scss:variables` command has the following options

##### `-D or --dir`
If you don't want to run the command on the files in your current directory (you can check the current directory by running `pwd` in the terminal).
Then you can provide the `--dir` option. The option understands both relative- and full-paths. Meaning `-D /home/user/project/assets/scss` is valid, but when running in `/home/user/project` you can also do `--dir=assets/scss`.

```shell script
## Anywhere (With short-hand)
rico scss:variables -D /home/user/project/assets/scss

## Inside /home/user/project
rico scss:variables --dir=assets/scss
```

##### `-o or --out`
If you want the name of the output file to be different or you want it to be in a different directory or whatever. You can provide the `--out` option.
This option can receive both relative- and full-paths and defaults to `_generated-variables.scss`.
Meaning the variables created with the command will be placed in the current directory in a file called `_generated-variables.scss`.  

```shell script
## With short-hand
rico scss:variables -o _variables.scss

## Inside /home/user/project
rico scss:variables --out=_variables.scss
```

##### `-r or --rule`
The `--rule` option is the most important option of all. This option defines which `css` rules (`color`, `background`, etc.) will be converted to variables.
It doesn't have a default value. **The command will not do anything if you don't provide any values.**

```shell script
code14 scss:colors -r color -r background --rule=background-color --rule=border
```

### Personal Commands
Create a command in `app/Commands/PersonalCommands` and it will load for you, but not ever be committed 😈