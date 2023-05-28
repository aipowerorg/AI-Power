<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion7" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-7">
  <div id="accordion7">
  <h3>DALL-E</h3>
        <div>
          <p>DALL-E is a deep learning model developed by OpenAI to generate digital images from natural language descriptions, called "prompts".</p>
          <p>You can generate astonishing images from simple text prompts by using Image Generator feature.</p>
          <ol>
            <li>Go to Image Generator and enter a prompt in the text field.</li>
            <li>Click on the "Generate" button.</li>
            <li>Wait for the images to be generated.</li>
            <li>You can select the image you like the most and click on the "Save to Media" button to save the image to your WordPress Media Library.</li>
          </ol>
          <p><b>Image Size:</b> You can select the size of the images you want to generate. DALL-E supports <b>256x256</b>, <b>512x512</b> and <b>1024x1024</b> image sizes.</p>
          <p><b>Number of Images:</b> You can select the number of images you want to generate. DALL-E supports up to <b>6</b> images per request.</p>
          <p>Please note that not all requests made to OpenAI will result in an image being returned. OpenAI filters both prompts and images in accordance with their content policy, and if either is flagged, an error will be returned.</p>
          <p>This is the error message that will be displayed if the request is not in compliance with OpenAI's content policy:</p>
          <p><i>"Your request was rejected as a result of our safety system. Your prompt may contain text that is not allowed by our safety system."</i></p>
        </div>
  <h3>Stable Diffusion</h3>
        <div>
          <p>Stable Diffusion is a deep learning, text-to-image model.</p>
          <p>You can generate astonishing images from simple text prompts by using Image Generator feature.</p>
          <ol>
            <li>First make sure you have the API key for Stable Diffusion and you have entered it in the plugin settings.</li>
            <li>If you don't have an API key, you can get one from <a href="https://replicate.com/signin?next=/account" target="_blank">here</a>.</li>
            <li>Go to Image Generator and switch to "Stable Diffusion" tab.</li>
            <li>Enter a prompt in the text field.</li>
            <li>Click on the "Generate" button.</li>
            <li>Wait for the images to be generated.</li>
            <li>You can select the image you like the most and click on the "Save to Media" button to save the image to your WordPress Media Library.</li>
          </ol>
          <p><b>Image Size:</b> You can select the size of the images you want to generate. Stable Diffusion supports size from <b>128x128</b> to <b>1024x1024</b>.</p>
          <p><b>Number of Images:</b> You can generate up to <b>2</b> images per request.</p>
          <p><b>Negative Prompt:</b> You can enter a negative prompt to exclude certain words from the generated image. Seperate multiple words with a comma.</p>
          <p><b>Prompt Strength:</b> Strength is a value between 0.0 and 1.0, that controls the amount of noise that is added to the input image.</p>
          <p><b>Number of Inference Steps:</b> Number of denoising steps. Minimum: 1; maximum: 500.</p>
          <p><b>Guidance Scale:</b> Scale for classifier-free guidance. Minimum: 1; maximum: 20.</p>
          <p><b>Scheduler:</b> Options are: DDIM, K_EULER, DPMSolverMultistep, K_EULER_ANCESTRAL, PNDM, KLMS.
        </div>
  <h3>Settings</h3>
        <div>
          <p><b>Artist:</b> Our plugin offers over 40 artist names in the dropdown menu, that you can choose from.</p>  
          <p>When you select an artist, such as Salvador Dali, selected AI engine will generate an image in that artist's style based on your prompt.</p>
          <p>For example, if your prompt is <i>"A robot with a cat-like design"</i> and you select Salvador Dali as the artist, the final prompt would read "A robot with a cat-like design Artist: Salvador Dali".</p>
          <p>If you don't want to select an artist, you can leave the dropdown menu on "None".</p>
          <p>Artists that you can choose from are:</p>
          <p><i>Albrecht Dürer, Alfred Sisley, Amedeo Modigliani, Andrea Mantegna, Andy Warhol, Camille Pissarro, Caravaggio, Caspar David Friedrich, Cézanne, Claude Monet, Diego Velázquez, Eugène Delacroix, Frida Kahlo, Gustav Klimt, Henri Matisse, Henri de Toulouse-Lautrec, Jackson Pollock, Jasper Johns, Joan Miró, Johannes Vermeer, Leonardo da Vinci, Mary Cassatt, Michelangelo, Pablo Picasso, Paul Cézanne, Paul Gauguin, Paul Klee, Pierre-Auguste Renoir, Pieter Bruegel the Elder, Piet Mondrian, Raphael, René Magritte, Salvador Dalí, Sandro Botticelli, Theo van Gogh, Titian, Vincent van Gogh, Vassily Kandinsky, Winslow Homer.</i></p>
          <p><b>Style:</b> Our plugin offers over 50 style names in the dropdown menu, that you can choose from.</p>
          <p>When you select a style, such as "Abstract", selected AI engine will generate an image in that style based on your prompt.</p>
          <p>Art styles that you can choose from are:</p>
          <p><i>Abstract, Abstract Expressionism, Art Brut, Art Deco, Art Nouveau, Baroque, Byzantine, Classical, Color Field, Conceptual, Cubism, Dada, Expressionism, Fauvism, Figurative, Futurism, Gothic, Hard-edge painting, Hyperrealism, Impressionism, Japonisme, Luminism, Lyrical Abstraction, Mannerism, Minimalism, Naive Art, Neo-expressionism, Neo-pop, New Realism, Op Art, Opus Anglicanum, Outsider Art, Photorealism, Pointillism, Pop Art, Post-Impressionism, Realism, Renaissance, Rococo, Romanticism, Street Art, Superflat, Surrealism, Symbolism, Tenebrism, Ukiyo-e, Western Art, YBA.</i></p>
          <p><b>Photography:</b> We have more than 30 photography styles that you can choose from.</p>
          <p><i>Abstract, Action, Aerial, Agricultural, Animal, Architectural, Artistic, Astrophotography, Bird photography, Black and white, Candid, Cityscape, Close-up, Commercial, Conceptual, Corporate, Documentary, Event, Family, Fashion, Fine art, Food, Food photography, Glamour, Industrial, Landscape, Lifestyle, Macro, Nature, Night, Portrait, Product, Sports, Street, Travel, Underwater, Wedding, Wildlife.</i></p>
          <p><b>Lightning:</b> We have more than 50 lightning styles that you can choose from.</p>
          <p><i>Ambient, Artificial light, Backlight, Black light, Blue hour, Candle light, Chiaroscuro, Cloudy, Color gels, Continuous light, Contre-jour, Direct light, Direct sunlight, Diffused light, Firelight, Flat light, Fluorescent, Fog, Front light, Golden hour, Hard light, Hazy sunlight, High key, Incandescent, Key light, LED, Low key, Moonlight, Natural light, Neon, Open shade, Overcast, Paramount, Party lights, Photoflood, Quarter light, Reflected light, Rim light, Shaded, Shaded light, Silhouette, Side light, Single-source, Softbox, Soft light, Split lighting, Stage lighting, Studio light, Sunburst, Tungsten, Umbrella lighting, Underexposed, Venetian blinds, Warm light, White balance.</i></p>
          <p><b>Subject:</b> We have more than 10 subjects that you can choose from.</p>
          <p><i>Abstract, Action, Animals, Architecture, Candid, Cars, Cityscapes, Events, Flowers, Food, Landscapes, Nature, Night, People, Portrait, Seascapes, Still life, Street, Underwater, Wildlife.</i></p>
          <p><b>Camera:</b> We have more than 40 camera settings that you can choose from.</p>
          <p><i>Active D-Lighting, Aperture, Aspect Ratio, Audio Recording, Auto Exposure Bracketing, Auto Focus Mode, Auto Focus Point, Auto ISO, Auto Lighting Optimizer, Auto Rotate, Chromatic Aberration Correction, Color Space, Continuous Shooting, Distortion Correction, Drive Mode, Dynamic Range, Exposure Compensation, Flash Mode, Focus Mode, Focus Peaking, Frame Rate, GPS, Grid Overlay, High Dynamic Range, Highlight Tone Priority, Image Format, Image Stabilization, Interval Timer Shooting, ISO, ISO Auto Setting, Lens Correction, Live View, Long Exposure Noise Reduction, Manual Focus, Metering Mode, Movie Mode, Movie Quality, Noise Reduction, Picture Control, Picture Style, Quality, Self-Timer, Shutter Speed, Time-lapse Interval, Time-lapse Recording, Virtual Horizon, Video Format, White Balance, Zebra Stripes.</i></p>
          <p><b>Composition:</b> We have more than 50 composition styles that you can choose from.</p>
          <p><i>Rule of Thirds, Asymmetrical, Balance, Centered, Close-up, Color blocking, Contrast, Cropping, Diagonal, Documentary, Environmental Portrait", Fill the Frame, Framing, Golden Ratio, High Angle, Leading Lines, Long Exposure, Low Angle, Macro, Minimalism, Negative Space, Panning, Patterns, Photojournalism, Point of View, Portrait, Reflections, Saturation, Scale, Selective Focus, Shallow Depth of Field, Silhouette, Simplicity, Snapshot, Street Photography, Symmetry, Telephoto, Texture, Tilt-Shift, Time-lapse, Tracking Shot, Travel, Triptych, Ultra-wide, Vanishing Point, Viewpoint, Vintage, Wide Angle, Zoom Blur, Zoom In/Zoom Out.</i></p>
          <p><b>Resolution:</b> We have more 7 different resolution options that you can choose from.</p>
          <p><i>4K (3840x2160), 2K (2560x1440), 1080p (1920x1080), 720p (1280x720), 480p (854x480), 1080i (1920x1080), 720i (1280x720).</i></p>
          <p><b>Color:</b> We have more 20 different color options that you can choose from.</p>
          <p><i>RGB, CMYK, Grayscale, HEX, Pantone, CMY, HSL, HSV, LAB, LCH, LUV, XYZ, YUV, YIQ, YCbCr, YPbPr, YDbDr, YCoCg, YCgCo, YCC.</i></p>
        </div>
  <h3>Shortcodes</h3>
        <div>
          <p>To show the image generator on your post or page, simply insert the following shortcode.</p>
          <p>To display both DALL-E and Stable Diffusion, use <code>[wpcgai_img]</code>.</p>
          <p>To show only DALL-E, use [wpcgai_img dalle=yes]</code>.</p>
          <p>To display only Stable Diffusion, use [wpcgai_img sd=yes]</code>.</p>
          <p>To show the settings, use <code>[wpcgai_img settings=yes]</code>, <code>[wpcgai_img dalle=yes settings=yes]</code>, or <code>[wpcgai_img sd=yes settings=yes]</code>.</p>
        </div>
  </div>
</div>