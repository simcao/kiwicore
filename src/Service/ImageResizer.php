<?php
/*
 *
 * This file is part of the Kiwicore package.
 *
 * (c) Simcao EI <dev@simcao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  2023
 */

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

/**
 * Class to resize image.
 *
 * @author Simcao
 */
class ImageResizer
{
    /**
     * Maximum width
     */
    private const MAX_WIDTH = 800;

    /**
     * Maximum height
     */
    private const MAX_HEIGHT = 500;

    /**
     * @var Imagine
     */
    private Imagine $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * Resize image using Imagine lib.
     *
     * @param string $filename
     * @return void
     * @throws \Exception
     */
    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);

        if (!$iheight > 0)
        {
            throw new \Exception('Le document a une taille égale à 0');
        }

        $ratio = $iwidth / $iheight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);
    }
}