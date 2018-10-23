<?php
namespace Dkf\Core\Plugin\Framework\App\PageCache;
use Magento\Framework\App\PageCache\Kernel as Sb;
use Magento\Framework\App\Response\Http as R;
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
			preg_match_all('~<\s*\blink\b[^>]*\/>~is', $html, $links);
			if ($links and isset($links[0]) and $links[0]) {
				$links[0] = array_reverse($links[0]);
				foreach ($links[0] as $l) {
					if (
						false === strpos($l, 'styles-m')
						&& false === strpos($l, 'styles-l')
						//&& false === strpos($l, 'layout_default')
						//&& false === strpos($l, 'design_default')
						&& false === strpos($l, 'Dkf_Core')
					) {
						$html = str_replace($l, '', $html);
						$html = str_ireplace("</body>", "$l</body>", $html);
					}
				}
			}
			preg_match_all('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', $html, $scripts);
			if ($scripts and isset($scripts[0]) and $scripts[0]) {
				$html = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $html);
				$scripts = implode("", $scripts[0]);
				$scripts = str_replace('<script>', '<script defer>', $scripts);
				$html = str_ireplace("</body>", "$scripts</body>", $html);
			}
			$r->setBody($html);
		}
		return [$r];
	}
}