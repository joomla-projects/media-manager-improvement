<?php
$name = JFactory::getApplication()->input->getCmd('name');
$filePath = $displayData['filePath'];

JHtml::_('jquery.framework');
JFactory::getDocument()->addScript('/media/plg_media-editor_imagefilters/js/caman.full.min.js');
JFactory::getDocument()->addScript('/media/plg_media-editor_imagefilters/js/caman-init.js');
JFactory::getDocument()->addStyleSheet('/media/plg_media-editor_imagefilters/css/caman.css');

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
<div class="btn-toolbar imagefilters-toolbar">


    <span id="joomla-media-image-filters" src="<?php echo $image; ?>" class="hidden" <?php echo 'data-url="'. $uploadUrl . '"'; ?>></span>
    <canvas id="filter-canvas" style="left:auto; right:auto;"></canvas>

    <div class="preset-filters" class="span12" >
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

    <div id="Filters" class="">

        <div class="span6">
            <div class="control-group">
                <label class="control-label" for="brightness">brightness</label>
                <div class="controls">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="brightness" id="brightness">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="contrast">contrast</label>
                <div class="controls">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="contrast" id="contrast">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="saturation">saturation</label>
                <div class="controls">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="saturation" id="saturation">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="vibrance">vibrance</label>
                <div class="controls">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="vibrance" id="vibrance">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="exposure">exposure</label>
                <div class="controls">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="exposure" id="exposure">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hue">hue</label>
                <div class="controls">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="hue" id="hue">
                </div>
            </div>
        </div>

        <div class="span6">
            <div class="control-group">
                <label class="control-label" for="sepia">sepia</label>
                <div class="controls">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="sepia" id="sepia">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="gamma">gamma</label>
                <div class="controls">
                    <input type="range" min="0" max="10" step="0.1" value="0" data-filter="gamma" id="gamma">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="noise">noise</label>
                <div class="controls">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="noise" id="noise">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="clip">clip</label>
                <div class="controls">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="clip" id="clip">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sharpen">sharpen</label>
                <div class="controls">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="sharpen" id="sharpen">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="tiltShift">tiltShift</label>
                <div class="controls">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="tiltShift" id="tiltShift">
                </div>
            </div>
        </div>
    </div>

    <div class="span-12 panel">
        <a class="btn btn-warning" data-preset="reset">Reset</a>

        <a class="btn btn-success" data-preset="save">Save changes</a>
    </div>
</div>

