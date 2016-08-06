<?php
$name = JFactory::getApplication()->input->getCmd('name');
$filePath = $displayData['filePath'];

JHtml::_('jquery.framework');
JFactory::getDocument()->addScript('/media/plg_media-editor_imagefilters/js/caman.full.min.js');
JFactory::getDocument()->addScript('/media/plg_media-editor_imagefilters/js/caman-init.js');
JFactory::getDocument()->addStyleSheet('/media/plg_media-editor_imagefilters/css/caman.css');

$image = COM_MEDIA_BASEURL . '/' . $filePath;
?>
<script type="text/javascript">

    var jFilter = Caman("#example", '<?php echo $image; ?>', function () {
             this.brightness(5);
             this.render();
         });

</script>

<div class="btn-toolbar imagefilters-toolbar">

    <img id="test-image" src="<?php echo $image; ?>" class="hidden"/>
    <canvas id="example" style="left:auto; right:auto;"></canvas>

    <div id="PresetFilters" class="span12" >
        <h3>Presets (Instagram)</h3>
        <a class="btn btn-primary" data-preset="vintage" onclick="return jFilter.render(function() {
      jFilter.vintage();
      return;
    })">Vintage</a>
        <a class="btn btn-primary" data-preset="lomo" onclick="return jFilter.render(function() {
      jFilter.clarity();
      return;
    })">Lomo</a>
        <a class="btn btn-primary" data-preset="clarity" onclick="return jFilter.render(function() {
      jFilter.vintage();
      return;
    })">Clarity</a>
        <a class="btn btn-primary" data-preset="sinCity" onclick="return jFilter.render(function() {
      jFilter.sinCity();
      return;
    })">Sin City</a>
        <a class="btn btn-primary" data-preset="sunrise" onclick="return jFilter.render(function() {
      jFilter.crossProcess();
      return;
    })">Sunrise</a>
        <a class="btn btn-primary" data-preset="crossProcess" onclick="return jFilter.render(function() {
      jFilter.vintage();
      return;
    })">Cross Process</a>
        <a class="btn btn-primary" data-preset="orangePeel" onclick="return jFilter.render(function() {
      jFilter.orangePeel();
      return;
    })">Orange Peel</a>
        <a class="btn btn-primary" data-preset="love" onclick="return jFilter.render(function() {
      jFilter.love();
      return;
    })">Love</a>
        <a class="btn btn-primary" data-preset="grungy" onclick="return jFilter.render(function() {
      jFilter.grungy();
      return;
    })">Grungy</a>
        <a class="btn btn-primary" data-preset="jarques" onclick="return jFilter.render(function() {
      jFilter.jarques();
      return;
    })">Jarques</a>
        <a class="btn btn-primary" data-preset="pinhole" onclick="return jFilter.render(function() {
      jFilter.pinhole();
      return;
    })">Pinhole</a>
        <a class="btn btn-primary" data-preset="oldBoot" onclick="return jFilter.render(function() {
      jFilter.oldBoot();
      return;
    })">Old Boot</a>
        <a class="btn btn-primary" data-preset="glowingSun" onclick="return jFilter.render(function() {
      jFilter.glowingSun();
      return;
    })">Glowing Sun</a>
        <a class="btn btn-primary" data-preset="hazyDays" onclick="return jFilter.render(function() {
      jFilter.hazyDays();
      return;
    })">Hazy Days</a>
        <a class="btn btn-primary" data-preset="herMajesty" onclick="return jFilter.render(function() {
      jFilter.herMajesty();
      return;
    })">Her Majesty</a>
        <a class="btn btn-primary" data-preset="nostalgia" onclick="return jFilter.render(function() {
      jFilter.nostalgia();
      return;
    })">Nostalgia</a>
        <a class="btn btn-primary" data-preset="hemingway" onclick="return jFilter.render(function() {
      jFilter.hemingway();
      return;
    })">Hemingway</a>
        <a class="btn btn-primary" data-preset="concentrate" onclick="jFilter.render(function() {
      jFilter.concentrate();
    })">Concentrate</a>

        <a class="btn btn-primary" data-preset="concentrate" onclick="
      jFilter.render(function() {
         jFilter.reset();
    })
">Reset</a>
    </div>

    <div id="Filters" class="hidden">

        <div class="span6">
            <div class="Filter">
                <div class="FilterName">
                    <p>brightness</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="brightness">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>contrast</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="contrast">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>saturation</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="saturation">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>vibrance</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="vibrance">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>exposure</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="exposure">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>hue</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="hue">
                    <span class="FilterValue">0</span>
                </div>
            </div>
        </div>

        <div class="span6">

            <div class="Filter">
                <div class="FilterName">
                    <p>sepia</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="sepia">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>gamma</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="10" step="0.1" value="0" data-filter="gamma">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>noise</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="noise">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>clip</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="clip">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>sharpen</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="sharpen">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>stackBlur</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="20" step="1" value="0" data-filter="stackBlur">
                    <span class="FilterValue">0</span>
                </div>
            </div>
        </div>

    </div>
</div>

