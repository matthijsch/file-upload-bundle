# FileUploadBundle


File Upload integration for Symfony 2.4+ and 3+.

## Installation using Composer
Run the following command to add the package to the composer.json of your project:

``` bash
$ composer require connectholland/file-upload-bundle
```

### Enable the bundle
Enable the bundle in the kernel:

``` php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new ConnectHolland\FileUploadBundle(),
        // ...
    );
}
```

## Usage


#### 1. Configure the FileUploadBundle for file uploads
The bundle requires a location to store the file uploads.
Configure the location with the existing FileUploadBundle configuration in your `config.yml` file:

``` yml
# app/config/config.yml

file_upload:
    path: "%kernel.root_dir%/../../some-directory-outside-of-the-project/%kernel.environment%"
```

#### 2. Modify your Doctrine entity class
To activate file uploads for a Doctrine entity you need to implement the `UploadObjectInterface` and add getters and setters for the form fields.

For ease of use the FileUploadBundle provides an `UploadTrait` to implement both the interface and the getters and setters:

``` php
namespace AppBundle\Entity;

use ConnectHolland\FileUploadBundle\Model\UploadObjectInterface;
use ConnectHolland\FileUploadBundle\Model\UploadTrait;

class Entity implements UploadObjectInterface
{
    use UploadTrait {
        getFileUpload as getImageUpload;
        setFileUpload as setImageUpload;
        getFileUpload as getAnotherImageUpload;
        setFileUpload as setAnotherImageUpload;
    }
}

```

In the above example you see the `UploadTrait` with getters and setters for two file upload fields implemented.
Here the `getImageUpload` method maps to a field called 'image' and `getAnotherImageUpload` maps to 'another_image'.


## Credits

- [Niels Nijens][link-author]
- [Matthijs Hasenpflug][link-author]
- [All Contributors][link-contributors]

### License

This package is licensed under the MIT License. Please see the [LICENSE file](LICENSE.md) for details.

[icon-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-symfony-form-documentation]: http://symfony.com/doc/current/book/forms.html
[link-author]: https://github.com/niels-nijens
[link-contributors]: ../../contributors