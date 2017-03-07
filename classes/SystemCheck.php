<?php

/**
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

namespace Cryptographp;

class SystemCheck
{
    /**
     * @return string
     */
    public function render()
    {
        global $plugin_tx;

        $o = '<h4>' . $plugin_tx['cryptographp']['syscheck_title'] . '</h4>'
            . $this->checkPHPVersion('5.1.2') . tag('br');
        foreach (array('gd', 'pcre', 'session', 'spl') as $ext) {
            $o .= $this->checkExtension($ext) . tag('br');
        }
        if (function_exists('gd_info')) {
            $o .= $this->checkGDSupport();
        }
        $o .= $this->checkMagicQuotesRuntime() . tag('br') . tag('br')
            . $this->checkXHVersion('1.6') . tag('br') . tag('br');
        foreach ($this->getWritableFolders() as $folder) {
            $o .= $this->checkWritability($folder) . tag('br');
        }
        return $o;
    }

    /**
     * @param string $version
     * @return string
     */
    protected function checkPHPVersion($version)
    {
        global $plugin_tx;

        $kind = version_compare(PHP_VERSION, $version) >= 0 ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['cryptographp']['syscheck_phpversion'], $version);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function checkExtension($name)
    {
        global $plugin_tx;

        $kind = extension_loaded($name) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['cryptographp']['syscheck_extension'], $name);
    }
    
    /**
     * @return string
     */
    protected function checkGDSupport()
    {
        global $plugin_tx;

        $html = '';
        $gdinfo = gd_info();
        if (!isset($gdinfo['JPEG Support'])) {
            $gdinfo['JPEG Support'] = $gdinfo['JPG Support'];
        }
        $support = array(
            array('FreeType Support', 'freetype'),
            array('GIF Create Support', 'gif'),
            array('JPEG Support', 'jpeg'),
            array('PNG Support', 'png')
        );
        foreach ($support as $i => $key) {
            $kind = $gdinfo[$key[0]] ? 'ok' : ($i < 1 ? 'fail' : 'warn');
            $html .= $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
                . $plugin_tx['cryptographp']['syscheck_' . $key[1] . '_support']
                . tag('br');
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function checkMagicQuotesRuntime()
    {
        global $plugin_tx;

        $kind = get_magic_quotes_runtime() ? 'fail' : 'ok';
        return $this->renderCheckIcon($kind). '&nbsp;&nbsp;'
            . $plugin_tx['cryptographp']['syscheck_magic_quotes'];
    }

    /**
     * @param string $version
     * @return string
     */
    protected function checkXHVersion($version)
    {
        global $plugin_tx;

        $kind = $this->hasXHVersion($version) ? 'ok' : 'fail';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['cryptographp']['syscheck_xhversion'], $version);
    }

    /**
     * @param string $version
     * @return bool
     */
    protected function hasXHVersion($version)
    {
        return defined('CMSIMPLE_XH_VERSION')
            && strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') === 0
            && version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH {$version}", 'gt');
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function checkWritability($filename)
    {
        global $plugin_tx;

        $kind = is_writable($filename) ? 'ok' : 'warn';
        return $this->renderCheckIcon($kind) . '&nbsp;&nbsp;'
            . sprintf($plugin_tx['cryptographp']['syscheck_writable'], $filename);
    }

    /**
     * @param string $kind
     * @return string
     */
    protected function renderCheckIcon($kind)
    {
        global $pth, $plugin_tx;

        $path = $pth['folder']['plugins'] . 'cryptographp/images/'
            . $kind . '.png';
        $alt = $plugin_tx['cryptographp']['syscheck_alt_' . $kind];
        return tag('img src="' . $path  . '" alt="' . $alt . '"');
    }

    /**
     * @return array
     */
    protected function getWritableFolders()
    {
        global $pth;

        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'cryptographp/' . $folder;
        }
        return $folders;
    }
}
