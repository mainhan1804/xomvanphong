<?php
/**
 * RuTube.php
 *
 * @package Providers
 * @author Michael Pratt <pratt@hablarmierda.net>
 * @link   http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Embera\Providers;

/**
 * The RuTube Provider
 * @link http://rutube.ru
 */
class RuTube extends \Embera\Adapters\Service
{
    /** inline {@inheritdoc} */
    protected $apiUrl = 'http://rutube.ru/api/oembed/?format=json';

    /** inline {@inheritdoc} */
    protected function validateUrl()
    {
        return (preg_match('~rutube\.ru/video/([0-9a-f]+)/?$~i', $this->url));
    }
}

?>
