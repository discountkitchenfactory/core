<?php
namespace Dkf\Core\Plugin\Framework\App\PageCache;
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\App\PageCache\Kernel as Sb;
use Magento\Framework\App\Response\Http as R;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface as ILayout;
// 2018-10-24
final class Kernel {
	/**
	 * 2018-10-24
	 * @see \Magento\Framework\App\PageCache\Kernel::process()
	 * @param Sb $sb
	 * @param R $r
	 * @return R[]
	 */
	function beforeProcess(Sb $sb, R $r) {
		$html = $r->getBody();
		if (false !== stripos($html, "</body>")) {
			// 2018-10-25
			// This block reduces the mobile's optimization score from 95 to 87,
			// but increases the desktop's optimization score from 84 to 85.
			/** @var bool $isCat */
			if (($isCat = self::isCategory())) {
				preg_match_all('~<\s*\blink\b[^>]*\/>~is', $html, $links);
				if ($links && isset($links[0]) && $links[0]) {
					foreach ($links[0] as $l) {
						/*if (
							false !== strpos($l, 'styles-m')
						) {
							$html = str_replace($l, '', $html);
						}
						else
						*/
						if (
							false === strpos($l, 'rel="icon"')
						) {
							$html = str_replace($l, '', $html);
							$html = str_ireplace("</body>", "$l</body>", $html);
						}
					}
				}
			}
			preg_match_all('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', $html, $s);
			if ($s and isset($s[0]) and $s[0]) {
				if ($isCat) {
					$s[0] = array_filter($s[0], function($s) {return
						false === strpos($s, 'requirejs/mixins')
						&& false === strpos($s, 'requirejs-config')
						&& false === strpos($s, 'var require = {')
						&& false === strpos($s, 'text/x-magento-init')
					;});
					$s[0] = array_map(function($s) {return
						false === strpos($s, 'requirejs-min-resolver') ? $s :
							"<script>var ctx = require.s.contexts._, origNameToUrl = ctx.nameToUrl;ctx.nameToUrl = function () {var url = origNameToUrl.apply (ctx, arguments);if (!url.match (/\/tiny_mce\//)) {url = url.replace (/(\.min)?\.js$/, '.min.js');}return url;};</script>"
					;}, $s[0]);
					$s[0] = array_map(function($s) {return
						false === strpos($s, 'requirejs/require') ? $s :
							"<script src='https://code.jquery.com/jquery-1.12.4.min.js'/>"
					;}, $s[0]);
				}
				$html = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $html);
				$s = implode("", $s[0]);
				//$s = str_replace('<script>', '<script async>', $s);
				if (false && $isCat) {
					$s = str_replace(
						'<script type="text/x-magento-init">', '<script async type="text/x-magento-init">', $s
					);
					/*$s = str_replace(
						'<script type="text/javascript">', '<script async type="text/javascript">', $s
					);*/
					$s = str_replace(
						'<script type="text/javascript"', '<script async type="text/javascript"', $s
					);
				}
				$html = str_ireplace("</body>", "$s</body>", $html);
			}
			$r->setBody($html);
		}
		return [$r];
	}

	/**
	 * 2015-12-21
	 * @used-by beforeProcess()
	 * @used-by \CleverSoft\Base\Model\Renderer::renderAssetHtml()
	 * @return string[]
	 */
	static function isCategory() {return in_array('catalog_category_view', self::handles());}

	/**
	 * 2015-12-21
	 * @used-by isCategory()
	 * @return string[]
	 */
	private static function handles() {
		$om = OM::getInstance(); /** @var OM $om */
		$l = $om->get(ILayout::class);  /** @var ILayout|Layout $l */
		return ($u = $l->getUpdate()) ? $u->getHandles() : [];
	}
}