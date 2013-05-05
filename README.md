TinyMceTinyImagesBundle
=======================

Very simple TinyMCE 3 image manager

Features
--------
* single file upload
* list images
* remove images
* insert images with double click

Image Entity
------------
see https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/index.md#annotate-entities
```php
<?php
namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="tinymce_images")
 *
 * @Vich\Uploadable
 */
class TinyMceImage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Vich\UploadableField(mapping="tinymce_image", fileNameProperty="name")
     *
     * @var \Symfony\Component\HttpFoundation\File\File $file
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, name="name")
     *
     * @var string $name
     */
    protected $name;

    // ...
}
```

Configuration
-------------

```yaml
# app/config/config.yml
# see https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/index.md#configuration-reference
vich_uploader:
    db_driver: orm
    mappings:
        tinymce_image:
            uri_prefix:                         /images
            upload_destination:                 %kernel.root_dir%/../web/images
            delete_on_remove:                   true
            delete_on_update:                   false

# see https://github.com/avalanche123/AvalancheImagineBundle
avalanche_imagine:
    driver:                                     gd
    filters:
        tinymce_image:
            type:                               thumbnail
            options:
                cache_type:                     public
                cache_expires:                  2 weeks
                size:                           [100, 100]
                mode:                           outbound
                quality:                        90
                format:                         jpg

# see https://github.com/stfalcon/TinymceBundle
stfalcon_tinymce:
    # ...
    theme:
        advanced:
            plugins:                            "-tinymcetinyimages"
            theme_advanced_buttons1 :           "tinymcetinyimages"
    # ...
    external_plugins:
        imagemanager:
            url:                                "asset[/tiny-mce-tiny-images/js/editor_plugin.js]"

ask_tiny_mce_tiny_images:
    image_class:                                "Acme\\DemoBundle\\Entity\\TinyMceImage"
    thumbnail_filter:                           "tinymce_image" # avalanche_imagine filter name
    image_field:                                "file" # image entity file field name
```