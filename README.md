# Profile Split Enable
This module allows Drupal the installation of multiple sites on the same codebase to use multiple profiles. When combined with CMI and config split we can have a config split defined for each profile.

## Purpose

Theoretically, using multi-site Drupal 8, you might want to have multiple profiles. Then, you can install various sites on your multi-site platform from any one of the profiles. Going a step farther, you likely want to have configuration for each of the profiles.

There is an issue currently between drupal/core, drupal/config_split, and acquia/blt that isn't allowing config splits at the profile level to work. 

The issue, fundamentally, is that a config split model assumes a default set of configuration that is overwritten in the various splits. This works great for single profile models. However with a multi-profile model, this immediately breaks down because drupal/core writes the profile key into core.extensions.yml. The result? As soon as you try to import config on a site with an active profile that is not the profile in core.extensions.yml you get the following error:

```
The selected installation profile <em class="placeholder">profile_1</em> does not match the profile stored in configuration <em class="placeholder">profile</em>
```

This module attempts to remedy the situation by filtering the current profile name when Drupal reads or writes to the core.extension file on disk so that the currently installed profile never conflicts with the profile in your default configuration.

Additionally, if you are using `config_split`, it will enable a split that matches the name of your installed profile at run time. This allows you to have configuration specifically enabled per split.

## Requirements

This module requires Drupal 8 and the `config_filter` module. You will likely use `config_split` to allow different config per profile.

## Installation

This module is distributed through packagist therefore should be installed using composer.

`composer require nedsbeds/profile_split_enable ^1.0.0`

## Usage

For this module to function, you MUST add it as a dependency of your profile, and then ensure it is also enabled in config (i.e. your `core.extenstion` file)

This is vital since the module must be enabled before and after you attempt to import your configuration.

**example_profile/example_profile.info.yml**
```yaml
name: 'Example Profile'
type: profile
description: 'Example lightning sub profile.'
version: 1.0.0
base profile: lightning
core: 8.x
dependencies:
  - profile_split_enable
```

**config/default/core.extension.yml**
```yaml
module:
    ...
    config_filter: 0
    config_split: 0
    ...
    profile_split_enable: 0
    ...
```

### Multiple Profiles

For basic functionality this module only requires Drupal 8 and the `config_filter` module. Once enabled, it will allow you to install Drupal using a profile that is different to the one specified in your default core.extension file.

You will likely want to use a profile such as `acquia/lightning` which allows you to create sub-profiles (https://docs.acquia.com/lightning/subprofile). It is recommended that you use `acquia/blt` to setup your drupal project since sub-profiles currently need a patch to Drupal core to function.

### Profile Splits

After the first installation of Drupal with your base profile, enable the `config_split` module and create splits for each of your profiles. The machine name of the split should match the machine name of your profile.

## Limitations/Outstanding features
This module does not currently support installation from existing configuration. You are expected to be using a workflow that involves installing Drupal from a profile and then importing configuration. This workflow is the approach that tools such as Acquia BLT and Acquia ACSF utilise. 

