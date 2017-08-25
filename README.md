[![Build Status](https://scrutinizer-ci.com/g/gplcart/demo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/demo/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/demo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/demo/?branch=master)

Demo is a [GPL Cart](https://github.com/gplcart/gplcart) module that allows superadmin to populate blank stores with realistic content such as products, categories, banners etc. It's a great way to test different themes and present your sites to clients. When you don't need demo anymore you can easily delete all the created data with only one click!

**Features:**

- Supports any number of demo packages
- You can add more packages from a module
- You can remove created content any time in just one click
- Command line support

**Extending:**

Use hook `module.demo.handlers`

**CLI commands**

- `php gplcart demo-create` - create a demo content
- `php gplcart demo-delete` - delete an existing demo content

The following options available for both commands:

- `--store` - Numeric store ID, defaults to 1
- `--package` - Package (handler) ID, defaults to `default`

**Note:**

- Only super admin can create/delete demo content from UI
- Don't forget to disable the module on production to prevent conflicts with store settings


**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/demo`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/tool/demo` and create demo content
