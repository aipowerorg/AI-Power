<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="tabs-2">
    <div class="wpcgai_form_row">
        <p><?php 
echo  esc_html__( 'This tab allows you to set and save default values for both Express Mode and Auto Content Writer. Changes made here will be applied to both modules.', 'gpt3-ai-content-generator' ) ;
?></p>
        <p><b><?php 
echo  esc_html__( 'Language, Style and Tone', 'gpt3-ai-content-generator' ) ;
?></b></p>
        <label class="wpcgai_label">Language:</label>
        <select class="regular-text" id="label_wpai_language"  name="wpaicg_settings[wpai_language]" >
            <option value="en" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'en' ? 'selected' : '' ) ;
?>>English</option>
            <option value="af" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'af' ? 'selected' : '' ) ;
?>>Afrikaans</option>
            <option value="ar" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ar' ? 'selected' : '' ) ;
?>>Arabic</option>
            <option value="an" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'an' ? 'selected' : '' ) ;
?>>Armenian</option>
            <option value="bs" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'bs' ? 'selected' : '' ) ;
?>>Bosnian</option>
            <option value="bg" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'bg' ? 'selected' : '' ) ;
?>>Bulgarian</option>
            <option value="zh" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'zh' ? 'selected' : '' ) ;
?>>Chinese (Simplified)</option>
            <option value="zt" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'zt' ? 'selected' : '' ) ;
?>>Chinese (Traditional)</option>
            <option value="hr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'hr' ? 'selected' : '' ) ;
?>>Croatian</option>
            <option value="cs" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'cs' ? 'selected' : '' ) ;
?>>Czech</option>
            <option value="da" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'da' ? 'selected' : '' ) ;
?>>Danish</option>
            <option value="nl" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'nl' ? 'selected' : '' ) ;
?>>Dutch</option>
            <option value="et" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'et' ? 'selected' : '' ) ;
?>>Estonian</option>
            <option value="fil" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'fil' ? 'selected' : '' ) ;
?>>Filipino</option>
            <option value="fi" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'fi' ? 'selected' : '' ) ;
?>>Finnish</option>
            <option value="fr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'fr' ? 'selected' : '' ) ;
?>>French</option>
            <option value="de" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'de' ? 'selected' : '' ) ;
?>>German</option>
            <option value="el" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'el' ? 'selected' : '' ) ;
?>>Greek</option>
            <option value="he" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'he' ? 'selected' : '' ) ;
?>>Hebrew</option>
            <option value="hi" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'hi' ? 'selected' : '' ) ;
?>>Hindi</option>
            <option value="hu" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'hu' ? 'selected' : '' ) ;
?>>Hungarian</option>
            <option value="id" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'id' ? 'selected' : '' ) ;
?>>Indonesian</option>
            <option value="it" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'it' ? 'selected' : '' ) ;
?>>Italian</option>
            <option value="ja" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ja' ? 'selected' : '' ) ;
?>>Japanese</option>
            <option value="ko" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ko' ? 'selected' : '' ) ;
?>>Korean</option>
            <option value="lv" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'lv' ? 'selected' : '' ) ;
?>>Latvian</option>
            <option value="lt" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'lt' ? 'selected' : '' ) ;
?>>Lithuanian</option>
            <option value="ms" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ms' ? 'selected' : '' ) ;
?>>Malay</option>
            <option value="no" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'no' ? 'selected' : '' ) ;
?>>Norwegian</option>
            <option value="fa" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'fa' ? 'selected' : '' ) ;
?>>Persian</option>
            <option value="pl" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'pl' ? 'selected' : '' ) ;
?>>Polish</option>
            <option value="pt" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'pt' ? 'selected' : '' ) ;
?>>Portuguese</option>
            <option value="ro" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ro' ? 'selected' : '' ) ;
?>>Romanian</option>
            <option value="ru" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ru' ? 'selected' : '' ) ;
?>>Russian</option>
            <option value="sr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sr' ? 'selected' : '' ) ;
?>>Serbian</option>
            <option value="sk" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sk' ? 'selected' : '' ) ;
?>>Slovak</option>
            <option value="sl" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sl' ? 'selected' : '' ) ;
?>>Slovenian</option>
            <option value="es" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'es' ? 'selected' : '' ) ;
?>>Spanish</option>
            <option value="sv" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sv' ? 'selected' : '' ) ;
?>>Swedish</option>
            <option value="th" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'th' ? 'selected' : '' ) ;
?>>Thai</option>
            <option value="tr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'tr' ? 'selected' : '' ) ;
?>>Turkish</option>
            <option value="uk" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'uk' ? 'selected' : '' ) ;
?>>Ukranian</option>
            <option value="vi" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'vi' ? 'selected' : '' ) ;
?>>Vietnamese</option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/language-style-tone#language" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Writing Style:</label>
        <select class="regular-text" id="label_wpai_writing_style" name="wpaicg_settings[wpai_writing_style]" >
            <option value="infor" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'infor' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Informative', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="acade" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'acade' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Academic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="analy" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'analy' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Analytical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="anect" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'anect' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Anecdotal', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="argum" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'argum' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Argumentative', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="artic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'artic' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Articulate', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="biogr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'biogr' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Biographical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="blog" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'blog' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Blog', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="casua" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'casua' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Casual', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="collo" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'collo' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Colloquial', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="compa" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'compa' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Comparative', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="conci" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'conci' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Concise', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="creat" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'creat' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Creative', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="criti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'criti' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Critical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="descr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'descr' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Descriptive', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="detai" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'detai' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Detailed', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="dialo" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'dialo' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Dialogue', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="direct" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'direct' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Direct', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="drama" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'drama' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Dramatic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="evalu" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'evalu' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Evaluative', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="emoti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'emoti' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Emotional', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="expos" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'expos' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Expository', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="ficti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'ficti' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Fiction', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="histo" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'histo' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Historical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="journ" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'journ' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Journalistic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="lette" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'lette' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Letter', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="lyric" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'lyric' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Lyrical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="metaph" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'metaph' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Metaphorical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="monol" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'monol' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Monologue', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="narra" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'narra' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Narrative', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="news" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'news' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'News', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="objec" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'objec' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Objective', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="pasto" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'pasto' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Pastoral', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="perso" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'perso' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Personal', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="persu" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'persu' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Persuasive', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="poeti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'poeti' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Poetic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="refle" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'refle' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Reflective', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="rheto" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'rheto' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Rhetorical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="satir" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'satir' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Satirical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="senso" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'senso' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Sensory', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="simpl" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'simpl' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Simple', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="techn" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'techn' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Technical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="theore" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'theore' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Theoretical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="vivid" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'vivid' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Vivid', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="busin" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'busin' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Business', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="repor" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'repor' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Report', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="resea" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'resea' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Research', 'gpt3-ai-content-generator' ) ;
?></option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/language-style-tone#writing-style" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Writing Tone', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <select class="regular-text" id="label_wpai_writing_tone" name="wpaicg_settings[wpai_writing_tone]" >
            <option value="formal" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'formal' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Formal', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="asser" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'asser' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Assertive', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="authoritative" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'authoritative' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Authoritative', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="cheer" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'cheer' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Cheerful', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="confident" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'confident' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Confident', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="conve" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'conve' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Conversational', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="factual" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'factual' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Factual', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="friendly" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'friendly' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Friendly', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="humor" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'humor' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Humorous', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="informal" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'informal' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Informal', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="inspi" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'inspi' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Inspirational', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="neutr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'neutr' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Neutral', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="nostalgic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'nostalgic' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Nostalgic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="polite" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'polite' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Polite', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="profe" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'profe' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Professional', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="romantic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'romantic' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Romantic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="sarca" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sarca' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Sarcastic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="scien" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'scien' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Scientific', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="sensit" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sensit' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Sensitive', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="serious" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'serious' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Serious', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="sincere" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sincere' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Sincere', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="skept" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'skept' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Skeptical', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="suspenseful" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'suspenseful' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Suspenseful', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="sympathetic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sympathetic' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Sympathetic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="curio" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'curio' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Curious', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="disap" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'disap' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Disappointed', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="encou" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'encou' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Encouraging', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="optim" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'optim' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Optimistic', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="surpr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'surpr' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Surprised', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="worry" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'worry' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Worried', 'gpt3-ai-content-generator' ) ;
?></option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/language-style-tone#writing-tone" target="_blank">?</a>
    </div>
    <hr>
    <p><b><?php 
echo  esc_html__( 'Headings', 'gpt3-ai-content-generator' ) ;
?></b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Number of Headings', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <input type="number" min="1" max="15" class="regular-text" id="label_wpai_number_of_heading"  name="wpaicg_settings[wpai_number_of_heading]" value="<?php 
echo  esc_html( $existingValue['wpai_number_of_heading'] ) ;
?>" placeholder="e.g. 5" >
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/headings#number-of-headings" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Heading Tag', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <select class="regular-text" id="label_wpai_heading_tag" name="wpaicg_settings[wpai_heading_tag]" >
            <option value="h1" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/headings#heading-tag" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Outline Editor', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <input type="checkbox" id="label_wpai_modify_headings" name="wpaicg_settings[wpai_modify_headings]"
               value="<?php 
echo  esc_html( $existingValue['wpai_modify_headings'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_modify_headings'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/headings#outline-editor" target="_blank">?</a>
    </div>
    <hr>
    <p><b><?php 
echo  esc_html__( 'Additional Content', 'gpt3-ai-content-generator' ) ;
?></b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Add Tagline?', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <input type="checkbox" id="label_wpai_add_tagline" name="wpaicg_settings[wpai_add_tagline]"
               value="<?php 
echo  esc_html( $existingValue['wpai_add_tagline'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_add_tagline'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/additional-content#enable-or-disable-tagline" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Add Q&A?', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <?php 
?>
            <input type="checkbox" value="0" disabled><?php 
echo  esc_html__( 'Available in Pro', 'gpt3-ai-content-generator' ) ;
?>
            <?php 
?>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/qa#enable-or-disable-qa" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Make Keywords Bold?', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <?php 
?>
            <input type="checkbox" value="0" disabled class="pro_chk"><?php 
echo  esc_html__( 'Available in Pro', 'gpt3-ai-content-generator' ) ;
?>
            <?php 
?>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/keywords#add-keywords" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Call-to-Action Position', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <select class="regular-text" id="label_wpai_cta_pos" name="wpaicg_settings[wpai_cta_pos]" >
            <option value="beg" <?php 
echo  ( esc_html( $existingValue['wpai_cta_pos'] ) == 'beg' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'Beginning', 'gpt3-ai-content-generator' ) ;
?></option>
            <option value="end" <?php 
echo  ( esc_html( $existingValue['wpai_cta_pos'] ) == 'end' ? 'selected' : '' ) ;
?>><?php 
echo  esc_html__( 'End', 'gpt3-ai-content-generator' ) ;
?></option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/links#adding-call-to-action" target="_blank">?</a>
    </div>
    <hr>
    <p><strong><?php 
echo  esc_html__( 'Table of Contents', 'gpt3-ai-content-generator' ) ;
?></strong></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Add ToC?', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <?php 
$wpaicg_toc = get_option( 'wpaicg_toc', false );
?>
        <input<?php 
echo  ( $wpaicg_toc ? ' checked' : '' ) ;
?> type="checkbox" value="1" name="wpaicg_toc">
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/table-of-contents#enable-or-disable-toc" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'ToC Title', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <?php 
$wpaicg_toc_title = get_option( 'wpaicg_toc_title', 'Table of Contents' );
?>
        <input type="text" class="regular-text" value="<?php 
echo  esc_html( $wpaicg_toc_title ) ;
?>" name="wpaicg_toc_title">
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/table-of-contents#customize-toc-title" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'ToC Title Tag', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <?php 
$wpaicg_toc_title_tag = get_option( 'wpaicg_toc_title_tag', 'h2' );
?>
        <select class="regular-text" name="wpaicg_toc_title_tag">
            <option value="h1" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/table-of-contents#toc-title-tag" target="_blank">?</a>
    </div>
    <hr>
    <p><b><?php 
echo  esc_html__( 'Introduction', 'gpt3-ai-content-generator' ) ;
?></b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Add Introduction?', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <input type="checkbox" id="label_wpai_add_intro" name="wpaicg_settings[wpai_add_intro]"
               value="<?php 
echo  esc_html( $existingValue['wpai_add_intro'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_add_intro'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/additional-content#enable-or-disable-introduction" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Intro Title Tag', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <?php 
$wpaicg_intro_title_tag = get_option( 'wpaicg_intro_title_tag', 'h2' );
?>
        <select class="regular-text" name="wpaicg_intro_title_tag">
            <option value="h1" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/additional-content#setting-the-heading-tag-for-introduction" target="_blank">?</a>
    </div>
    <?php 
$wpaicg_hide_conclusion = get_option( 'wpaicg_hide_conclusion', false );
$wpaicg_hide_introduction = get_option( 'wpaicg_hide_introduction', false );
?>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Hide Introduction Title', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <input type="checkbox" name="wpaicg_hide_introduction" value="1"<?php 
echo  ( $wpaicg_hide_introduction ? " checked" : "" ) ;
?>/>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/additional-content#show-or-hide-introduction-title" target="_blank">?</a>
    </div>
    <hr>
    <p><strong><?php 
echo  esc_html__( 'Conclusion', 'gpt3-ai-content-generator' ) ;
?></strong></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Add Conclusion?', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <input type="checkbox" id="label_wpai_add_conclusion" name="wpaicg_settings[wpai_add_conclusion]"
               value="<?php 
echo  esc_html( $existingValue['wpai_add_conclusion'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_add_conclusion'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/additional-content#enable-or-disable-conclusion" target="_blank">?</a>
    </div>
    <!-- wpaicg_conclusion_title_tag -->
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Conclusion Title Tag', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <?php 
$wpaicg_conclusion_title_tag = get_option( 'wpaicg_conclusion_title_tag', 'h2' );
?>
        <select class="regular-text" name="wpaicg_conclusion_title_tag">
            <option value="h1" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/additional-content#setting-the-heading-tag-for-conclusion" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php 
echo  esc_html__( 'Hide Conclusion Title', 'gpt3-ai-content-generator' ) ;
?>:</label>
        <input type="checkbox" name="wpaicg_hide_conclusion" value="1"<?php 
echo  ( $wpaicg_hide_conclusion ? " checked" : "" ) ;
?>/>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/additional-content#show-or-hide-conclusion-title" target="_blank">?</a>
    </div>
    <hr>
    <p><strong><?php 
echo  esc_html__( 'Custom Prompt for Express Mode', 'gpt3-ai-content-generator' ) ;
?></strong></p>
    <div class="wpcgai_form_row">
        <?php 
$wpaicg_content_custom_prompt_enable = get_option( 'wpaicg_content_custom_prompt_enable', false );
$wpaicg_content_custom_prompt = get_option( 'wpaicg_content_custom_prompt', '' );
if ( empty($wpaicg_content_custom_prompt) ) {
    $wpaicg_content_custom_prompt = \WPAICG\WPAICG_Custom_Prompt::get_instance()->wpaicg_default_custom_prompt;
}
?>
        <div class="mb-5">
            <label><input<?php 
echo  ( $wpaicg_content_custom_prompt_enable ? ' checked' : '' ) ;
?> type="checkbox" class="wpaicg_meta_custom_prompt_enable" name="wpaicg_content_custom_prompt_enable">&nbsp;Enable</label>
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/custom-prompt" target="_blank">?</a>
        </div>
        <div class="wpaicg_meta_custom_prompt_box" style="<?php 
echo  ( isset( $wpaicg_content_custom_prompt_enable ) && $wpaicg_content_custom_prompt_enable ? '' : 'display:none' ) ;
?>">
            <label><?php 
echo  esc_html__( 'Custom Prompt', 'gpt3-ai-content-generator' ) ;
?></label>
            <textarea rows="20" class="wpaicg_meta_custom_prompt" name="wpaicg_content_custom_prompt"><?php 
echo  esc_html( str_replace( "\\", '', $wpaicg_content_custom_prompt ) ) ;
?></textarea>
            <?php 

if ( \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ) {
    ?>
                <div>
                    <?php 
    echo  sprintf(
        esc_html__( 'Make sure to include %s in your prompt. You can also incorporate %s and %s to further customize your prompt.', 'gpt3-ai-content-generator' ),
        '<code>[title]</code>',
        '<code>[keywords_to_include]</code>',
        '<code>[keywords_to_avoid]</code>'
    ) ;
    ?>
                </div>
            <?php 
} else {
    ?>
                <div>
                    <?php 
    echo  sprintf( esc_html__( 'Ensure %s is included in your prompt.', 'gpt3-ai-content-generator' ), '<code>[title]</code>' ) ;
    ?>
                </div>
            <?php 
}

?>
            <button style="color: #fff;background: #df0707;border-color: #df0707;" data-prompt="<?php 
echo  esc_html( \WPAICG\WPAICG_Custom_Prompt::get_instance()->wpaicg_default_custom_prompt ) ;
?>" class="button wpaicg_meta_custom_prompt_reset" type="button"><?php 
echo  esc_html__( 'Reset', 'gpt3-ai-content-generator' ) ;
?></button>
            <div class="wpaicg_meta_custom_prompt_auto_error"></div>
        </div>
    </div>
</div>
