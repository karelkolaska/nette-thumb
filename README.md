# KarelKolaska\NetteThumbnails

Nette extension for generating locally stored thumbnails.

## Installation

Install KarelKolaska\NetteThumbnails using [Composer](https://getcomposer.org/):

```
$ composer require karelkolaska/nette-thumbnails
```

## Configuration

Register extension in your NEON configuration:

```
extensions:
  	thumb:	KarelKolaska\NetteThumb\DI\ThumbExtension
```

Set extension configuration and default thumbnails properties:

```
thumb:
	debugMode: FALSE
	thumbDir: %wwwDir%/images/thumbs
	notFoundImage: %wwwDir%/images/not-found-image.jpg
	defaultProps:
		articleList:
			width: 250
			height: 160
		productDetail:
			width: 500
			height: 400
			flag: exact
			watermark: /images/watermark.png
			watermarkLeft: 100%
			watermarkTop: 100%
			watermarkOpacity: 50         
```

### debugMode

When se to TRUE, thumbnails are re-generated with every hit. Otherwise thumbnails are generated only if original image modification time is greater then modification time of associated thumbnail.

### thumbDir

Directory path to store generated thumbnails.

### notFoundImage

Image that will be shown, when original image doesn't exists.

### defaultProps

Predefined thumbnail properties.

| **Property**      | **Description** |
| ----------------- | ------------- |
| width             | thumbnail width in px  |
| height            | thumbnail height in px  |
| flag              | represents image resize flags from [Nette\Utils\Image](https://doc.nette.org/en/2.4/images)  |
| watermark         | path to watermark  |
| watermarkLeft     | watermark position in thumbnail from left in % or px  |
| watermarkTop      | watermark position in thumbnail from top in % or px  |
| watermarkOpacity  | watermark opacity 0-100  |

## Usage

In template by custom thumbnail properties:

```
<img src="{$imagePath|thumb:['width' => 150, 'height' => 100]}" />	
```

Or by predefined properties from configuration:
```
<img src="{$imagePath|thumbProps:'productDetail'}" />
```

In presenter by injecting ThumbGenerator and setting custom properties:

```
$thumbPath = $this->thumbGenerator->setOrigPath('/path/to/image.png')
                      ->setProperties([
                        'width' => 150,
                        'height' => 100
                      ])->getThumbPath();
```

Or by passing predefined properties key:
		
```
$thumbPath = $this->thumbGenerator->setOrigPath('/path/to/image.png')					
                      ->loadPropsFromDefault('productDetail')
                      ->getThumbPath();
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/karelkolaska/nette-thumb/blob/master/LICENSE) file for details.
