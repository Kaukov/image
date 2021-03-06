<?php

namespace Spatie\Image;

use ReflectionClass;
use Spatie\Image\Exceptions\InvalidManipulation;

class Manipulations
{
    const CROP_TOP_LEFT = 'crop-top-left';
    const CROP_TOP = 'crop-top';
    const CROP_TOP_RIGHT = 'crop-top-right';
    const CROP_LEFT = 'crop-left';
    const CROP_CENTER = 'crop-center';
    const CROP_RIGHT = 'crop-right';
    const CROP_BOTTOM_LEFT = 'crop-bottom-left';
    const CROP_BOTTOM = 'crop-bottom';
    const CROP_BOTTOM_RIGHT = 'crop-bottom-right';

    const ORIENTATION_AUTO = 'auto';
    const ORIENTATION_90 = 90;
    const ORIENTATION_180 = 180;
    const ORIENTATION_270 = 270;

    const FIT_CONTAIN = 'contain';
    const FIT_MAX = 'max';
    const FIT_FILL = 'fill';
    const FIT_STRETCH = 'stretch';
    const FIT_CROP = 'crop';

    const BORDER_OVERLAY = 'overlay';
    const BORDER_SHRINK = 'shrink';
    const BORDER_EXPAND = 'expand';

    const FORMAT_JPG = 'jpg';
    const FORMAT_PJPG = 'pjpg';
    const FORMAT_PNG = 'png';
    const FORMAT_GIF = 'gif';

    const FILTER_GREYSCALE = 'greyscale';
    const FILTER_SEPIA = 'sepia';

    /** @var \Spatie\Image\ManipulationSequence */
    protected $manipulationSequence;

    public function __construct(array $manipulations = [])
    {
        $this->manipulationSequence = new ManipulationSequence($manipulations);
    }

    /**
     * @param string $orientation
     *
     * @return $this
     */
    public function orientation(string $orientation)
    {
        if (! $this->validateManipulation($orientation, 'orientation')) {
            throw InvalidManipulation::invalidParameter(
                'orientation',
                $orientation,
                $this->getValidManipulationOptions('orientation')
            );
        }

        return $this->addManipulation('orientation', $orientation);
    }

    /**
     * @param string $cropMethod
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function crop(string $cropMethod, int $width, int $height)
    {
        if (! $this->validateManipulation($cropMethod, 'crop')) {
            throw InvalidManipulation::invalidParameter(
                'cropmethod',
                $cropMethod,
                $this->getValidManipulationOptions('crop')
            );
        }

        $this->width($width);
        $this->height($height);

        return $this->addManipulation('crop', $cropMethod);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $focalX Crop center X in percent
     * @param int $focalY Crop center Y in percent
     *
     * @return $this
     */
    public function focalCrop(int $width, int $height, int $focalX, int $focalY)
    {
        $this->width($width);
        $this->height($height);

        return $this->addManipulation('crop', "crop-{$focalX}-{$focalY}");
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     *
     * @return $this
     */
    public function manualCrop(int $width, int $height, int $x, int $y)
    {
        if ($width < 0) {
            throw InvalidManipulation::invalidWidth($width);
        }

        if ($height < 0) {
            throw InvalidManipulation::invalidWidth($height);
        }

        return $this->addManipulation('manualCrop', "{$width},{$height},{$x},{$y}");
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function width(int $width)
    {
        if ($width < 0) {
            throw InvalidManipulation::invalidWidth($width);
        }

        return $this->addManipulation('width', $width);
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function height(int $height)
    {
        if ($height < 0) {
            throw InvalidManipulation::invalidWidth($height);
        }

        return $this->addManipulation('height', $height);
    }

    /**
     * @param string $fitMethod
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function fit(string $fitMethod, int $width, int $height)
    {
        if (! $this->validateManipulation($fitMethod, 'fit')) {
            throw InvalidManipulation::invalidParameter(
                'fit',
                $fitMethod,
                $this->getValidManipulationOptions('fit')
            );
        }

        $this->width($width);
        $this->height($height);

        return $this->addManipulation('fit', $fitMethod);
    }

    /**
     * @param int $ratio A value between 1 and 8
     *
     * @return $this
     */
    public function devicePixelRatio(int $ratio)
    {
        if ($ratio < 1 || $ratio > 8) {
            throw InvalidManipulation::valueNotInRange('ratio', $ratio, 1, 8);
        }

        return $this->addManipulation('devicePixelRatio', $ratio);
    }

    /**
     * @param int $brightness A value between -100 and 100
     *
     * @return $this
     */
    public function brightness(int $brightness)
    {
        if ($brightness < -100 || $brightness > 100) {
            throw InvalidManipulation::valueNotInRange('brightness', $brightness, -100, 100);
        }

        return $this->addManipulation('brightness', $brightness);
    }

    /**
     * @param float $gamma A value between 0.01 and 9.99
     *
     * @return $this
     */
    public function gamma(float $gamma)
    {
        if ($gamma < 0.01 || $gamma > 9.99) {
            throw InvalidManipulation::valueNotInRange('gamma', $gamma, 0.01, 9.00);
        }

        return $this->addManipulation('gamma', $gamma);
    }

    /**
     * @param int $contrast A value between -100 and 100
     *
     * @return $this
     */
    public function contrast(int $contrast)
    {
        if ($contrast < -100 || $contrast > 100) {
            throw InvalidManipulation::valueNotInRange('contrast', $contrast, -100, 100);
        }

        return $this->addManipulation('contrast', $contrast);
    }

    /**
     * @param int $sharpen A value between 0 and 100
     *
     * @return $this
     */
    public function sharpen(int $sharpen)
    {
        if ($sharpen < 0 || $sharpen > 100) {
            throw InvalidManipulation::valueNotInRange('sharpen', $sharpen, 0, 100);
        }

        return $this->addManipulation('sharpen', $sharpen);
    }

    /**
     * @param int $blur A value between 0 and 100
     *
     * @return $this
     */
    public function blur(int $blur)
    {
        if ($blur < 0 || $blur > 100) {
            throw InvalidManipulation::valueNotInRange('blur', $blur, 0, 100);
        }

        return $this->addManipulation('blur', $blur);
    }

    /**
     * @param int $pixelate A value between 0 and 1000
     *
     * @return $this
     */
    public function pixelate(int $pixelate)
    {
        if ($pixelate < 0 || $pixelate > 1000) {
            throw InvalidManipulation::valueNotInRange('pixelate', $pixelate, 0, 1000);
        }

        return $this->addManipulation('pixelate', $pixelate);
    }

    /**
     * @return $this
     */
    public function greyscale()
    {
        return $this->filter('greyscale');
    }

    /**
     * @return $this
     */
    public function sepia()
    {
        return $this->filter('sepia');
    }

    /**
     * @param string $colorName
     *
     * @return $this
     */
    public function background(string $colorName)
    {
        return $this->addManipulation('background', $colorName);
    }

    /**
     * @param int $width
     * @param string $color
     * @param string $borderType
     *
     * @return $this
     */
    public function border(int $width, string $color, string $borderType = 'overlay')
    {
        if ($width < 0) {
            throw InvalidManipulation::invalidWidth($width);
        }

        if (! $this->validateManipulation($borderType, 'border')) {
            throw InvalidManipulation::invalidParameter(
                'border',
                $borderType,
                $this->getValidManipulationOptions('border')
            );
        }

        return $this->addManipulation('border', "{$width},{$color},{$borderType}");
    }

    /**
     * @param int $quality
     *
     * @return $this
     */
    public function quality(int $quality)
    {
        if ($quality < 0 || $quality > 100) {
            throw InvalidManipulation::valueNotInRange('quality', $quality, 0, 100);
        }

        return $this->addManipulation('quality', $quality);
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function format(string $format)
    {
        if (! $this->validateManipulation($format, 'format')) {
            throw InvalidManipulation::invalidParameter(
                'format',
                $format,
                $this->getValidManipulationOptions('format')
            );
        }

        return $this->addManipulation('format', $format);
    }

    /**
     * @param string $filterName
     *
     * @return $this
     */
    protected function filter(string $filterName)
    {
        if (! $this->validateManipulation($filterName, 'filter')) {
            throw InvalidManipulation::invalidParameter(
                'filter',
                $filterName,
                $this->getValidManipulationOptions('filter')
            );
        }

        return $this->addManipulation('filter', $filterName);
    }

    /**
     * @return $this
     */
    public function apply()
    {
        $this->manipulationSequence->startNewGroup();

        return $this;
    }

    public function removeManipulation(string $name)
    {
        $this->manipulationSequence->removeManipulation($name);
    }

    public function hasManipulation(string $manipulationName): bool
    {
        return ! is_null($this->getManipulationArgument($manipulationName));
    }

    /**
     * @param string $manipulationName
     *
     * @return string|null
     */
    public function getManipulationArgument(string $manipulationName)
    {
        foreach ($this->manipulationSequence->getGroups() as $manipulationSet) {
            if (array_key_exists($manipulationName, $manipulationSet)) {
                return $manipulationSet[$manipulationName];
            }
        }
    }

    protected function addManipulation(string $manipulationName, string $manipulationArgument)
    {
        $this->manipulationSequence->addManipulation($manipulationName, $manipulationArgument);

        return $this;
    }

    public function mergeManipulations(Manipulations $manipulations)
    {
        $this->manipulationSequence->merge($manipulations->manipulationSequence);

        return $this;
    }

    public function getManipulationSequence(): ManipulationSequence
    {
        return $this->manipulationSequence;
    }

    protected function validateManipulation(string $value, string $constantNamePrefix): bool
    {
        return in_array($value, $this->getValidManipulationOptions($constantNamePrefix));
    }

    protected function getValidManipulationOptions(string $manipulation): array
    {
        $options = (new ReflectionClass(static::class))->getConstants();

        return array_filter($options, function ($value, $name) use ($manipulation) {
            return strpos($name, strtoupper($manipulation)) === 0;
        }, ARRAY_FILTER_USE_BOTH);
    }
}
