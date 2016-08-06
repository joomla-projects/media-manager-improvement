<?php
$name = JFactory::getApplication()->input->getCmd('name');
$filePath = $displayData['filePath'];

//JHtml::_('jquery.framework');
// IE8-9 need a polyfill to work with this plugin, also if that polyfill is loaded we don't need jQuery
JHtml::_('script', 'plg_media-editor_imagefilters/caman.full.min.js', false, true, false, false, true);
JHtml::_('script', 'plg_media-editor_imagefilters/caman-init.js', false, true, false, false, true);
JHtml::_('stylesheet', 'plg_media-editor_imagefilters/caman.css', array(), true);

$image = COM_MEDIA_BASEURL . '/' . $filePath;

$path_parts = pathinfo($filePath);
$folder     =$path_parts['dirname'];

$session    = JFactory::getSession();
$uploadUrl  = JUri::base() . 'index.php?option=com_media&task=file.upload&tmpl=component&allowovewrite=true&folder='
	. $folder . '&'
	. $session->getName() . '=' . $session->getId()
	. '&' . JSession::getFormToken() . '=1'
	. '&asset=image&format=json';
?>
<div>
	<input type="submit" class="hidden" />
	<span id="joomla-media-image-filters" data-src="<?php echo $image; ?>" class="hidden" <?php echo 'data-url="'. $uploadUrl . '"'; ?>></span>
	<canvas id="filter-canvas" style="left:auto; right:auto;"></canvas>

	<hr/>

	<div class="preset-filters row" >
		<div class="span-12 well">
			<h3>Presets (Instagram)</h3>
			<a class="btn btn-primary" data-preset="vintage">Vintage</a>
			<a class="btn btn-primary" data-preset="lomo">Lomo</a>
			<a class="btn btn-primary" data-preset="clarity">Clarity</a>
			<a class="btn btn-primary" data-preset="sinCity">Sin City</a>
			<a class="btn btn-primary" data-preset="sunrise">Sunrise</a>
			<a class="btn btn-primary" data-preset="crossProcess">Cross Process</a>
			<a class="btn btn-primary" data-preset="orangePeel">Orange Peel</a>
			<a class="btn btn-primary" data-preset="love">Love</a>
			<a class="btn btn-primary" data-preset="grungy">Grungy</a>
			<a class="btn btn-primary" data-preset="jarques">Jarques</a>
			<a class="btn btn-primary" data-preset="pinhole">Pinhole</a>
			<a class="btn btn-primary" data-preset="oldBoot">Old Boot</a>
			<a class="btn btn-primary" data-preset="glowingSun">Glowing Sun</a>
			<a class="btn btn-primary" data-preset="hazyDays">Hazy Days</a>
			<a class="btn btn-primary" data-preset="herMajesty">Her Majesty</a>
			<a class="btn btn-primary" data-preset="nostalgia">Nostalgia</a>
			<a class="btn btn-primary" data-preset="hemingway">Hemingway</a>
			<a class="btn btn-primary" data-preset="concentrate">Concentrate</a>
		</div>
	</div>

	<div id="Filters" class="row">
		<div class="span-12 well">
			<h3 class="center">Manual adjustments</h3>
			<div class="span-6">
				<div class="control-group">
					<label class="control-label" for="brightness">brightness</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="-100" max="100" step="1" value="0" data-filter="brightness" id="brightness">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="contrast">contrast</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="-100" max="100" step="1" value="0" data-filter="contrast" id="contrast">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="saturation">saturation</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="-100" max="100" step="1" value="0" data-filter="saturation" id="saturation">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="vibrance">vibrance</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="-100" max="100" step="1" value="0" data-filter="vibrance" id="vibrance">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="exposure">exposure</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="-100" max="100" step="1" value="0" data-filter="exposure" id="exposure">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="hue">hue</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="0" max="100" step="1" value="0" data-filter="hue" id="hue">
					</div>
				</div>
			</div>

			<div class="span-6">
				<div class="control-group">
					<label class="control-label" for="sepia">sepia</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="0" max="100" step="1" value="0" data-filter="sepia" id="sepia">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="gamma">gamma</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="0" max="10" step="0.1" value="0" data-filter="gamma" id="gamma">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="noise">noise</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="0" max="100" step="1" value="0" data-filter="noise" id="noise">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="clip">clip</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="0" max="100" step="1" value="0" data-filter="clip" id="clip">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="sharpen">sharpen</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="0" max="100" step="1" value="0" data-filter="sharpen" id="sharpen">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="tiltShift">tiltShift</label>
					<div class="controls">
						<span class="range-value">0 </span>
						<input type="range" min="-100" max="100" step="1" value="0" data-filter="tiltShift" id="tiltShift">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="preset-filters well">
		<a class="btn btn-warning" data-preset="reset">Reset</a>
		<a class="btn btn-success" data-preset="save">Save changes</a>
	</div>
</div>