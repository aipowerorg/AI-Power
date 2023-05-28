<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
    .wpaicg_grid_form {
        grid-template-columns: repeat(3,1fr);
        grid-column-gap: 20px;
        grid-row-gap: 20px;
        display: grid;
        grid-template-rows: auto auto;
        margin-top: 20px;
    }
    .wpaicg_grid_form_2 {
        grid-column: span 2/span 1;
    }
    .wpaicg_grid_form_1 {
        grid-column: span 1/span 1;
    }
    .wpaicg-collapse:last-of-type {
        border-bottom: 1px solid #ccc;
    }
    .wpaicg-collapse-title {
        padding: 10px;
        background: #fff;
        border-top: 1px solid #ccc;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        font-size: 14px;
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: bold;
    }
    .wpaicg-collapse-content {
        display: block;
        background: #f1f1f1;
        padding: 10px;
        border-top: 1px solid #ccc;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
    }
    .wpaicg-collapse-content .wpaicg-form-label {
        display: inline-block;
        width: 50%;
    }
    .wpaicg-collapse-content select, .wpaicg-collapse-content input[type=number],.wpaicg-collapse-content input[type=text] {
        display: inline-block!important;
        width: 48%!important;
    }
    .wpaicg-overlay {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: rgb(0 0 0 / 20%);
        top: 0;
    }
    .wpaicg_modal {
        width: 900px;
        min-height: 100px;
        position: absolute;
        top: 5%;
        background: #fff;
        left: calc(50% - 450px);
        border-radius: 5px;
    }
    .wpaicg_modal_head {
        min-height: 30px;
        border-bottom: 1px solid #ccc;
        display: flex;
        align-items: center;
        padding: 6px 12px;
    }
    .wpaicg_modal_title {
        font-size: 18px;
    }
    .wpaicg_modal_close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
        width: 20px;
        height: 20px;
        line-height: unset;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }
    .wpaicg_modal_content {
        padding: 10px;
    }
    .wpaicg-button{
        padding: 5px 10px;
        background: #424242;
        border: 1px solid #343434;
        border-radius: 4px;
        color: #fff;
        font-size: 15px;
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .wpaicg-button:disabled{
        background: #505050;
        border-color: #999;
    }
    .wpaicg-button:hover:not(:disabled),.wpaicg-button:focus:not(:disabled){
        color: #fff;
        background-color: #171717;
        text-decoration: none;
    }
    .wpaicg-image-generator-tabs{
        display: block;
        margin-bottom: 10px;
        border-bottom: 1px solid #ccc;
    }
    .wpaicg-image-generator-tabs a{
        color: #333;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        padding: 10px 15px;
        border-top: 1px solid #ccc;
        margin-right: 10px;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        position: relative;
        top: 1px;
        background: #e1e1e1;
    }
    .wpaicg-image-generator-tabs a.wpaicg-tab-active{
        background: #fff;
    }
    @media(max-width: 1024px){
        .wpaicg_grid_form {
            grid-template-columns: repeat(1, 1fr);
        }
        .wpaicg_grid_form_2 {
            grid-column: span 1/span 1;
        }
    }
</style>
<?php
$wpaicg_image_shortcode = true;
include __DIR__.'/wpaicg_image_generator.php';
?>
