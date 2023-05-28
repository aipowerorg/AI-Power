let wpaicgInit = {
    wpaicgImageForm: null,
    wpaicgImageGenerated: null,
    wpaicgImageGrid: null,
    wpaicgImageLoading: null,
    wpaicgImageSaveBtn: null,
    wpaicgImageMessage: null,
    wpaicgImageConvertBar: null,
    wpaicg_image_modal_close: null,
    wpaicgNumberImages: null,
    wpaicgImageGenerateBtn: null,
    wpaicgImageSelectAll: null,
    wpaicgStartTime: null,
    init: function (){
        this.search();
        this.image();
        return this;
    },
    search: function(){
        let that = this;
        let wpaicgSearchs = document.getElementsByClassName('wpaicg-search');
        if(wpaicgSearchs && wpaicgSearchs.length){
            for(let i=0;i<wpaicgSearchs.length;i++){
                let wpaicgSearch = wpaicgSearchs[i];
                let wpaicgSearchForm = wpaicgSearch.getElementsByClassName('wpaicg-search-form')[0];
                let wpaicgSearchField = wpaicgSearch.getElementsByClassName('wpaicg-search-field')[0];
                let wpaicgSearchResult = wpaicgSearch.getElementsByClassName('wpaicg-search-result')[0];
                let wpaicgSearchSource = wpaicgSearch.getElementsByClassName('wpaicg-search-source')[0];
                let wpaicgSearchBtn = wpaicgSearch.getElementsByClassName('wpaicg-search-submit')[0];
                wpaicgSearchBtn.addEventListener('click', function (){
                    that.searchData(wpaicgSearchResult,wpaicgSearchSource,wpaicgSearchField);
                });
                wpaicgSearchForm.addEventListener('submit', function (e){
                    that.searchData(wpaicgSearchResult,wpaicgSearchSource,wpaicgSearchField);
                    e.preventDefault();
                    return false;
                })
            }
        }
    },
    searchExpand: function(element){
        let item = element.closest('.wpaicg-search-item');
        item.getElementsByClassName('wpaicg-search-item-excerpt')[0].style.display = 'none';
        item.getElementsByClassName('wpaicg-search-item-full')[0].style.display = 'block';
    },
    searchData: function (wpaicgSearchResult,wpaicgSearchSource,wpaicgSearchField){
        let search = wpaicgSearchField.value;
        if(search !== '') {
            wpaicgSearchResult.innerHTML = '<div class="wpaicg-search-loading"><div class="wpaicg-lds-dual-ring"></div></div>';
            wpaicgSearchSource.innerHTML = '';
            wpaicgSearchResult.classList.remove('wpaicg-has-item');
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', wpaicgParams.ajax_url);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send('action=wpaicg_search_data&_wpnonce='+wpaicgParams.search_nonce+'&search='+encodeURIComponent(search));
            xhttp.onreadystatechange = function(oEvent) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        wpaicgSearchResult.classList.add('wpaicg-has-item');
                        var wpaicg_response = this.responseText;
                        if (wpaicg_response !== '') {
                            wpaicg_response = JSON.parse(wpaicg_response);
                            wpaicgSearchResult.innerHTML = '';
                            if (wpaicg_response.status === 'success') {
                                if(wpaicg_response.data.length){
                                    for(let i = 0; i < wpaicg_response.data.length; i++){
                                        let item = wpaicg_response.data[i];
                                        wpaicgSearchResult.innerHTML += item;
                                    }
                                    if(wpaicg_response.source.length){
                                        wpaicgSearchSource.innerHTML = '<h3>'+wpaicgParams.languages.source+'</h3>';
                                        for(let i = 0; i < wpaicg_response.source.length; i++){
                                            let item = wpaicg_response.source[i];
                                            wpaicgSearchSource.innerHTML += item;
                                        }
                                    }
                                }
                                else{
                                    wpaicgSearchResult.innerHTML = '<p>'+wpaicgParams.languages.no_result+'</p>';
                                }
                            }
                            else{
                                wpaicgSearchResult.innerHTML = '<p class="wpaicg-search-error">'+wpaicg_response.msg+'</p>';
                            }
                        }
                        else{
                            wpaicgSearchResult.innerHTML = '<p class="wpaicg-search-error">'+wpaicgParams.languages.wrong+'</p>';
                        }
                    }
                    else{
                        wpaicgSearchResult.innerHTML = '<p class="wpaicg-search-error">'+wpaicgParams.languages.wrong+'</p>';
                    }
                }
            }
        }
    },
    imageModal: function (id){
        var item = document.getElementById('wpaicg-image-item-'+id);
        var alt = item.querySelectorAll('.wpaicg-image-item-alt')[0].value;
        var title = item.querySelectorAll('.wpaicg-image-item-title')[0].value;
        var caption = item.querySelectorAll('.wpaicg-image-item-caption')[0].value;
        var description = item.querySelectorAll('.wpaicg-image-item-description')[0].value;
        var url = item.querySelectorAll('input[type=checkbox]')[0].value;
        document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = '';
        document.querySelectorAll('.wpaicg-overlay')[0].style.display = 'block';
        document.querySelectorAll('.wpaicg_modal')[0].style.display = 'block';
        document.querySelectorAll('.wpaicg_modal_title')[0].innerHTML = wpaicgParams.languages.edit_image;
        var html = '<div class="wpaicg_grid_form">';
        html += '<div class="wpaicg_grid_form_2"><img src="'+url+'" style="width: 100%"></div>';
        html += '<div class="wpaicg_grid_form_1">';
        html += '<p><label>'+wpaicgParams.languages.alternative+'</label><input type="text" class="wpaicg_edit_item_alt" style="width: 100%" value="'+alt+'"></p>';
        html += '<p><label>'+wpaicgParams.languages.title+'</label><input type="text" class="wpaicg_edit_item_title" style="width: 100%" value="'+title+'"></p>';
        html += '<p><label>'+wpaicgParams.languages.caption+'</label><input type="text" class="wpaicg_edit_item_caption" style="width: 100%" value="'+caption+'"></p>';
        html += '<p><label>'+wpaicgParams.languages.description+'</label><textarea class="wpaicg_edit_item_description" style="width: 100%">'+description+'</textarea></p>';
        html += '<button onclick="wpaicgSaveImageData('+id+')" data-id="'+id+'" class="button button-primary wpaicg_edit_image_save" type="button">'+wpaicgParams.languages.save+'</button>';
        html += '</div>';
        html += '</div>';
        document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = html;
        wpaicgImageCloseModal();
    },
    image_generator: function(data, start, max, multi_steps,form_action){
        let that = this;
        const xhttp = new XMLHttpRequest();
        xhttp.open('POST', wpaicgParams.ajax_url);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(data);
        xhttp.onreadystatechange = function(oEvent) {
            if (xhttp.readyState === 4) {
                if (xhttp.status === 200) {
                    var wpaicg_response = this.responseText;
                    res = JSON.parse(wpaicg_response);
                    if(res.status === 'success'){
                        for(var idx = 0; idx < res.imgs.length; idx++){
                            let idImageBox = idx;
                            if(multi_steps){
                                idImageBox = start -1;
                            }
                            var img = res.imgs[idx];
                            var html = '<div id="wpaicg-image-item-'+idImageBox+'" class="wpaicg-image-item wpaicg-image-item-'+idx+'" data-id="'+idImageBox+'">';
                            if(wpaicgParams.logged_in === '1') {
                                html += '<label><input data-id="' + idImageBox + '" class="wpaicg-image-item-select" type="checkbox" name="image_url" value="' + img + '"></label>';
                            }
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-alt" type="hidden" name="image_alt">';
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-title" type="hidden" name="image_title">';
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-caption" type="hidden" name="image_caption">';
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-description" type="hidden" name="image_description">';
                            if(wpaicgParams.logged_in === '1') {
                                html += '<img onclick="wpaicgInit.imageModal(' + idImageBox + ')" src="' + img + '">';
                            }
                            else {
                                html += '<img onclick="wpaicgViewModalImage(this)" src="' + img + '">';
                            }
                            html += '</div>';
                            that.wpaicgImageGrid.innerHTML += html;
                        }
                        if(multi_steps){
                            if(start === max){
                                wpaicgImageRmLoading(that.wpaicgImageGenerateBtn);
                                that.wpaicgImageSelectAll.classList.remove('selectall')
                                that.wpaicgImageSelectAll.innerHTML = wpaicgSelectAllText;
                                that.wpaicgImageSelectAll.style.display = 'block';
                                that.wpaicgImageLoading.style.display = 'none';
                                that.wpaicgImageSaveBtn.style.display = 'block';
                            }
                            else{
                                that.image_generator(data, start+1, max, multi_steps,form_action)
                            }
                        }
                        else{
                            if(form_action === 'wpaicg_image_generator'){
                                let endTime = new Date();
                                let timeDiff = endTime - that.wpaicgStartTime;
                                timeDiff = timeDiff/1000;
                                data += '&action=wpaicg_image_log&duration='+timeDiff+'&_wpnonce_image_log='+wpaicgImageNonce+'&shortcode=['+wpaicgImageShortcode+']&source_id='+wpaicgImageSourceID;
                                const xhttp = new XMLHttpRequest();
                                xhttp.open('POST', wpaicgParams.ajax_url);
                                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                xhttp.send(data);
                                xhttp.onreadystatechange = function (oEvent) {
                                    if (xhttp.readyState === 4) {

                                    }
                                }
                            }
                            wpaicgImageRmLoading(that.wpaicgImageGenerateBtn);
                            that.wpaicgImageSelectAll.classList.remove('selectall')
                            that.wpaicgImageSelectAll.innerHTML = '<?php echo esc_html($wpaicg_image_select_all_text)?>';
                            that.wpaicgImageSelectAll.style.display = 'block';
                            that.wpaicgImageLoading.style.display = 'none';
                            that.wpaicgImageSaveBtn.style.display = 'block';
                        }
                    }
                    else{
                        wpaicgImageRmLoading(that.wpaicgImageGenerateBtn);
                        that.wpaicgImageLoading.style.display = 'none';
                        let errorMessage = document.createElement('div');
                        errorMessage.style.color = '#f00';
                        errorMessage.classList.add('wpaicg-image-error');
                        errorMessage.innerHTML = res.msg;
                        that.wpaicgImageGenerated.prepend(errorMessage);
                        setTimeout(function (){
                            errorMessage.remove();
                        },3000);
                    }
                }
                else{
                    that.wpaicgImageLoading.style.display = 'none';
                    wpaicgImageRmLoading(that.wpaicgImageGenerateBtn);
                    alert('Something went wrong');
                }
            }
        }

    },
    save_image: function (items,start){
        let that = this;
        if(start >= items.length){
            that.wpaicgImageConvertBar.getElementsByTagName('small')[0].innerHTML = items.length+'/'+items.length;
            that.wpaicgImageConvertBar.getElementsByTagName('span')[0].style.width = '100%';
            that.wpaicgImageMessage.innerHTML = wpaicgParams.languages.save_image_success;
            wpaicgImageRmLoading(that.wpaicgImageSaveBtn);
            setTimeout(function (){
                that.wpaicgImageMessage.innerHTML = '';
            },2000)
        }
        else{
            var id = items[start];
            var item = document.getElementById('wpaicg-image-item-'+id);
            var data = 'action=wpaicg_save_image_media';
            data += '&image_alt='+item.querySelectorAll('.wpaicg-image-item-alt')[0].value;
            data += '&image_title='+item.querySelectorAll('.wpaicg-image-item-title')[0].value;
            data += '&image_caption='+item.querySelectorAll('.wpaicg-image-item-caption')[0].value;
            data += '&image_description='+item.querySelectorAll('.wpaicg-image-item-description')[0].value;
            data += '&image_url='+encodeURIComponent(item.querySelectorAll('.wpaicg-image-item-select')[0].value);
            data +='&nonce='+wpaicgImageSaveNonce;
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', wpaicgParams.ajax_url);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(data);
            xhttp.onreadystatechange = function(oEvent) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        var wpaicg_response = this.responseText;
                        res = JSON.parse(wpaicg_response);
                        if(res.status === 'success'){
                            var currentPos = start+1;
                            var percent = Math.ceil(currentPos*100/items.length);
                            that.wpaicgImageConvertBar.getElementsByTagName('small')[0].innerHTML = currentPos+'/'+items.length;
                            that.wpaicgImageConvertBar.getElementsByTagName('span')[0].style.width = percent+'%';
                            that.save_image(items, start+1);
                        }
                        else{
                            that.wpaicgImageConvertBar.classList.add('wpaicg_error');
                            wpaicgImageRmLoading(that.wpaicgImageSaveBtn);
                            alert(res.msg);
                        }
                    } else {
                        alert(wpaicgParams.languages.wrong);
                        that.wpaicgImageConvertBar.classList.add('wpaicg_error');
                        wpaicgImageRmLoading(that.wpaicgImageSaveBtn);
                    }
                }
            }
        }
    },
    image: function (){
        let that = this;
        let wpaicgImageForm = document.getElementById('wpaicg-image-generator-form');
        if(wpaicgImageForm){
            this.wpaicgImageForm = wpaicgImageForm;
            this.wpaicgImageGenerated = wpaicgImageForm.getElementsByClassName('image-generated')[0];
            this.wpaicgImageGrid = wpaicgImageForm.getElementsByClassName('image-grid')[0];
            this.wpaicgImageLoading = wpaicgImageForm.getElementsByClassName('image-generate-loading')[0];
            this.wpaicgImageSaveBtn = wpaicgImageForm.getElementsByClassName('image-generator-save')[0];
            this.wpaicgImageMessage = wpaicgImageForm.getElementsByClassName('wpaicg_message')[0];
            this.wpaicgImageConvertBar = wpaicgImageForm.getElementsByClassName('wpaicg-convert-bar')[0];
            this.wpaicg_image_modal_close = wpaicgImageForm.getElementsByClassName('wpaicg_image_modal_close');
            this.wpaicgNumberImages = wpaicgImageForm.querySelector('select[name=num_images]');
            this.wpaicgImageGenerateBtn = wpaicgImageForm.getElementsByClassName('wpaicg_button_generate')[0];
            this.wpaicgImageSelectAll = wpaicgImageForm.getElementsByClassName('wpaicg_image_select_all')[0];
            this.wpaicgImageSaveBtn.addEventListener('click', function (e) {
                var items = [];
                document.querySelectorAll('.wpaicg-image-item input[type=checkbox]').forEach(function (item) {
                    if (item.checked) {
                        items.push(item.getAttribute('data-id'));
                    }
                });
                if (items.length) {
                    that.wpaicgImageConvertBar.style.display = 'block';
                    that.wpaicgImageConvertBar.classList.remove('wpaicg_error');
                    that.wpaicgImageConvertBar.getElementsByTagName('small')[0].innerHTML = '0/' + items.length;
                    that.wpaicgImageConvertBar.getElementsByTagName('span')[0].style.width = 0;
                    that.wpaicgImageMessage.innerHTML = '';
                    wpaicgImageLoadingEffect(that.wpaicgImageSaveBtn);
                    that.save_image(items, 0);
                } else {
                    alert(wpaicgParams.languages.select_save_error);
                }
            })
            this.wpaicgImageSelectAll.addEventListener('click', function (e) {
                if (that.wpaicgImageSelectAll.classList.contains('selectall')) {
                    that.wpaicgImageSelectAll.classList.remove('selectall');
                    that.wpaicgImageSelectAll.innerHTML = wpaicgSelectAllText;
                    document.querySelectorAll('.wpaicg-image-item input[type=checkbox]').forEach(function (item) {
                        item.checked = false;
                    })
                } else {
                    that.wpaicgImageSelectAll.classList.add('selectall');
                    that.wpaicgImageSelectAll.innerHTML = wpaicgParams.languages.unselect;
                    document.querySelectorAll('.wpaicg-image-item input[type=checkbox]').forEach(function (item) {
                        item.checked = true;
                    })
                }
            });
            wpaicgImageForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var form_action = wpaicgImageForm.querySelectorAll('input[name=action]')[0].value;
                if (form_action === 'wpaicg_image_stable_diffusion') {
                    that.wpaicgNumberImages = wpaicgImageForm.querySelector('select[name=num_outputs]');
                }
                var num_images = parseInt(that.wpaicgNumberImages.value);
                if (num_images > 0) {
                    var wpaicg_error = false;
                    if (form_action === 'wpaicg_image_stable_diffusion') {
                        var prompt_strength = parseFloat(document.getElementById('prompt_strength').value);
                        var num_inference_steps = parseFloat(document.getElementById('num_inference_steps').value);
                        var guidance_scale = parseFloat(document.getElementById('guidance_scale').value);
                        if (prompt_strength < 0 || prompt_strength > 1) {
                            wpaicg_error = wpaicgParams.languages.prompt_strength
                        } else if (num_inference_steps < 1 || num_inference_steps > 500) {
                            wpaicg_error = wpaicgParams.languages.num_inference_steps
                        } else if (guidance_scale < 1 || guidance_scale > 20) {
                            wpaicg_error = wpaicgParams.languages.guidance_scale
                        }
                    }
                    if (wpaicg_error) {
                        alert(wpaicg_error);
                    } else {
                        const queryString = new URLSearchParams(new FormData(wpaicgImageForm)).toString();
                        that.wpaicgImageSaveBtn.style.display = 'none';
                        wpaicgImageLoadingEffect(that.wpaicgImageGenerateBtn);
                        that.wpaicgImageConvertBar.style.display = 'none';
                        that.wpaicgImageLoading.style.display = 'flex';
                        that.wpaicgImageGrid.innerHTML = '';
                        that.wpaicgImageSelectAll.style.display = 'none';
                        let wpaicgImageError = document.getElementsByClassName('wpaicg-image-error');
                        if (wpaicgImageError.length) {
                            wpaicgImageError[0].remove();
                        }
                        if (form_action === 'wpaicg_image_stable_diffusion') {
                            that.image_generator(queryString, 1, num_images, true, form_action);
                        } else {
                            that.wpaicgStartTime = new Date();
                            that.image_generator(queryString, 1, num_images, false, form_action);
                        }
                    }
                } else {
                    alert(wpaicgParams.languages.error_image)
                }
                return false;
            });
        }
    }
}
window['wpaicgInit'] = wpaicgInit.init();

