# Cryptographp\_XH

Cryptographp\_XH is a CAPTCHA plugin
based on cryptographp by Sylvain Brison.
It was modified to fit into the CMSimple\_XH environment,
can be called multiple times on the same page,
and offers alternative audio CAPTCHAs.
It can be used as utility plugin by other plugins,
such as [Advancedform\_XH](https://github.com/cmb69/advancedform_xh),
to deter spambots from successfully submitting forms.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Cryptographp\_XH is a plugin for [CMSimple\_XH](https://www.cmsimple-xh.org/).
It requires CMSimple\_XH ≥ 1.7.0
and PHP ≥ 5.4.0 with the gd extension.

## Download

The [lastest release](https://github.com/cmb69/cryptographp/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple\_XH plugins.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole directory `cryptographp/` to your server into
   the `plugins/` directory of CMSimple\_XH.
1. Set write permissions to the subdirectories `config/`,
   `css/` and `languages/`.
1. Navigate to `Plugins` → `Cryptographp` in the back-end to check if
   all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple\_XH
plugins in the back-end of the Website.
Go to `Plugins` → `Cryptographp`.

You can change the default settings of Cryptographp\_XH under `Config`.
Hints for the options will be displayed
when hovering over the help icon with your mouse.

Localization is done under `Language`.
You can translate the character strings to your own language,
if there is no appropriate language file available,
or customize them according to your needs.

The look of Cryptographp\_XH can be customized under `Stylesheet`.

## Usage

Cryptographp\_XH is a utility plugin serving other plugins.
As such it has no usage on its own.
How it can be used by developers of other plugins is explained in the
[CMSimple\_XH Wiki](https://www.cmsimple-xh.org/wiki/doku.php/captcha_plugins).

## Limitations

To successfully submit forms protected by Cryptographp\_XH,
the browser of the visitor must accept cookies.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/cryptographp/issues)
or in the [CMSimple\_XH Forum](https://cmsimpleforum.com/).

## License

Cryptographp\_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Cryptographp\_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Cryptographp\_XH.  If not, see <https://www.gnu.org/licenses/>.

Copyright 2011-2021 Christoph M. Becker

Slovak translation © 2012 Dr. Martin Sereday  
Czech translation © 2012 Josef Němec  
Russian translation © 2012 Lybomyr Kydray

## Credits

Cryptographp\_XH is based on cryptographp.
Many thanks to Sylvain Brison for making this nice and quite
flexible CAPTCHA available under a GPL compatible license.
The audio CAPTCHA was inspired by braillecaptcha by *johnjdoe*,
and made possible by the phantastic
[MP3 files](https://www.theblog.ca/mp3-audio-files-alphabet) from Peter Keung,
which have been released as freeware.

The plugin logo is designed by
[Pavel InFeRnODeMoN](https://store.kde.org/u/InFeRnODeMoN).
Many thanks for releasing this icon under GPL.

The icons used in the front-end are designed by
[Google](https://material.io/icons/).
Many thanks for releasing the icons under Apache License Version 2.0.

Many thanks to the community at the
[CMSimple\_XH Forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Particulary I want to thank *snafu*,
who pointed me to cryptographp,
and *oldnema*,
who contributed the first set of language files for the audio CAPTCHA.

And last but not least many thanks to
[Peter Harteg](https://www.harteg.dk/), the “father” of CMSimple,
and all developers of [CMSimple\_XH](https://www.cmsimple-xh.org)
without whom this amazing CMS would not exist.
