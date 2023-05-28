var wpaicgPlayGround = {
    init: function(){
        var wpaicg_PlayGround = this;
        var wpaicgFormsShortcode = document.getElementsByClassName('wpaicg-playground-shortcode');
        var wpaicgClearButtons = document.getElementsByClassName('wpaicg-prompt-clear');
        var wpaicgStopButtons = document.getElementsByClassName('wpaicg-prompt-stop-generate');
        var wpaicgSaveButtons = document.getElementsByClassName('wpaicg-prompt-save-draft');
        var wpaicgDownloadButtons = document.getElementsByClassName('wpaicg-prompt-download');
        if(wpaicgDownloadButtons && wpaicgDownloadButtons.length){
            for(var i=0;i < wpaicgDownloadButtons.length;i++) {
                var wpaicgDownloadButton = wpaicgDownloadButtons[i];
                wpaicgDownloadButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    var wpaicgDownloadButton = e.currentTarget;
                    var wpaicgForm = wpaicgDownloadButton.closest('.wpaicg-prompt-form');
                    var formID = wpaicgForm.getAttribute('data-id');
                    var wpaicgFormData = window['wpaicgForm'+formID];
                    var currentContent = wpaicg_PlayGround.getContent(wpaicgFormData.response,formID);
                    var element = document.createElement('a');
                    currentContent = currentContent.replace(/<br>/g,"\n");
                    currentContent = currentContent.replace(/<br \/>/g,"\n");
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(currentContent));
                    element.setAttribute('download', 'ai-response.txt');

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);
                });
            }
        }
        if(wpaicgClearButtons && wpaicgClearButtons.length){
            for(var i=0;i < wpaicgClearButtons.length;i++){
                var wpaicgClearButton = wpaicgClearButtons[i];
                wpaicgClearButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    var wpaicgClearButton = e.currentTarget;
                    var wpaicgForm = wpaicgClearButton.closest('.wpaicg-prompt-form');
                    var formID = wpaicgForm.getAttribute('data-id');
                    var wpaicgFormData = window['wpaicgForm'+formID];
                    var wpaicgSaveResult = wpaicgForm.getElementsByClassName('wpaicg-prompt-save-result')[0];
                    wpaicg_PlayGround.setContent(wpaicgFormData.response,formID,'');
                    wpaicgSaveResult.style.display = 'none';
                });
            }
        }
        if(wpaicgStopButtons && wpaicgStopButtons.length){
            for(var i=0;i < wpaicgStopButtons.length;i++){
                var wpaicgStopButton = wpaicgStopButtons[i];
                wpaicgStopButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    var wpaicgStopButton = e.currentTarget;
                    var wpaicgForm = wpaicgStopButton.closest('.wpaicg-prompt-form');
                    var eventID = wpaicgStopButton.getAttribute('data-event');
                    var wpaicgSaveResult = wpaicgForm.getElementsByClassName('wpaicg-prompt-save-result')[0];
                    var wpaicgGenerateBtn = wpaicgForm.getElementsByClassName('wpaicg-generate-button')[0];
                    wpaicg_PlayGround.eventClose(eventID,wpaicgStopButton,wpaicgSaveResult,wpaicgGenerateBtn);
                });
            }
        }
        if(wpaicgSaveButtons && wpaicgSaveButtons.length){
            for(var i=0;i < wpaicgSaveButtons.length;i++){
                var wpaicgSaveButton = wpaicgSaveButtons[i];
                wpaicgSaveButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    var wpaicgSaveButton = e.currentTarget;
                    var wpaicgForm = wpaicgSaveButton.closest('.wpaicg-prompt-form');
                    var formID = wpaicgForm.getAttribute('data-id');
                    var wpaicgFormData = window['wpaicgForm'+formID];
                    var title = wpaicgForm.getElementsByClassName('wpaicg-prompt-post_title')[0].value;
                    var content = wpaicg_PlayGround.getContent(wpaicgFormData.response,formID);
                    if (title === '') {
                        alert('Please insert title');
                    } else if (content === '') {
                        alert('Please wait generate content')
                    } else {
                        const xhttp = new XMLHttpRequest();
                        xhttp.open('POST', wpaicgFormData.ajax);
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.send('action=wpaicg_save_draft_post_extra&title=' + title + '&content=' + content+'&save_source=promptbase&nonce='+wpaicgFormData.ajax_nonce);
                        wpaicg_PlayGround.loading.add(wpaicgSaveButton);
                        xhttp.onreadystatechange = function (oEvent) {
                            if (xhttp.readyState === 4) {
                                wpaicg_PlayGround.loading.remove(wpaicgSaveButton);
                                if (xhttp.status === 200) {
                                    var wpaicg_response = this.responseText;
                                    wpaicg_response = JSON.parse(wpaicg_response);
                                    if (wpaicg_response.status === 'success') {
                                        window.location.href = wpaicgFormData.post+'?post=' + wpaicg_response.id + '&action=edit';
                                    } else {
                                        alert(wpaicg_response.msg);
                                    }
                                } else {
                                    alert('Something went wrong');
                                }
                            }
                        }
                    }
                });
            }
        }
        if(wpaicgFormsShortcode && wpaicgFormsShortcode.length){
            for(var i = 0;i< wpaicgFormsShortcode.length;i++){
                var wpaicgFormShortcode =  wpaicgFormsShortcode[i];
                var wpaicgForm = wpaicgFormShortcode.getElementsByClassName('wpaicg-prompt-form')[0];
                wpaicgForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var wpaicgForm = e.currentTarget;
                    var formID = wpaicgForm.getAttribute('data-id');
                    var formSource = wpaicgForm.getAttribute('data-source');
                    var wpaicgFormData = window['wpaicgForm'+formID];
                    var wpaicgMaxToken = wpaicgForm.getElementsByClassName('wpaicg-prompt-max_tokens')[0];
                    var wpaicgTemperature = wpaicgForm.getElementsByClassName('wpaicg-prompt-temperature')[0];
                    var wpaicgTopP = wpaicgForm.getElementsByClassName('wpaicg-prompt-top_p')[0];
                    var wpaicgBestOf = wpaicgForm.getElementsByClassName('wpaicg-prompt-best_of')[0];
                    var wpaicgFP = wpaicgForm.getElementsByClassName('wpaicg-prompt-frequency_penalty')[0];
                    var wpaicgPP = wpaicgForm.getElementsByClassName('wpaicg-prompt-presence_penalty')[0];
                    var wpaicgMaxLines = wpaicgForm.getElementsByClassName('wpaicg-prompt-max-lines')[0];
                    var wpaicgPromptTitle = wpaicgForm.getElementsByClassName('wpaicg-prompt-title')[0];
                    var wpaicgPromptTitleFilled = wpaicgForm.getElementsByClassName('wpaicg-prompt-title-filled')[0];
                    var wpaicgGenerateBtn = wpaicgForm.getElementsByClassName('wpaicg-generate-button')[0];
                    var wpaicgSaveResult = wpaicgForm.getElementsByClassName('wpaicg-prompt-save-result')[0];
                    var wpaicgStop = wpaicgForm.getElementsByClassName('wpaicg-prompt-stop-generate')[0];
                    var max_tokens = wpaicgMaxToken.value;
                    var temperature = wpaicgTemperature.value;
                    var top_p = wpaicgTopP.value;
                    var best_of = wpaicgBestOf.value;
                    var frequency_penalty = wpaicgFP.value;
                    var presence_penalty = wpaicgPP.value;
                    var error_message = false;
                    var title = wpaicgPromptTitle.value;
                    if (title === '') {
                        error_message = 'Please insert prompt';
                    } else if (max_tokens === '') {
                        error_message = 'Please enter max tokens';
                    } else if (parseFloat(max_tokens) < 1 || parseFloat(max_tokens) > 8000) {
                        error_message = 'Please enter a valid max tokens value between 1 and 8000';
                    } else if (temperature === '') {
                        error_message = 'Please enter temperature';
                    } else if (parseFloat(temperature) < 0 || parseFloat(temperature) > 1) {
                        error_message = 'Please enter a valid temperature value between 0 and 1';
                    } else if (top_p === '') {
                        error_message = 'Please enter Top P';
                    } else if (parseFloat(top_p) < 0 || parseFloat(top_p) > 1) {
                        error_message = 'Please enter a valid Top P value between 0 and 1';
                    } else if (best_of === '') {
                        error_message = 'Please enter best of';
                    } else if (parseFloat(best_of) < 1 || parseFloat(best_of) > 20) {
                        error_message = 'Please enter a valid best of value between 0 and 1';
                    } else if (frequency_penalty === '') {
                        error_message = 'Please enter frequency penalty';
                    } else if (parseFloat(frequency_penalty) < 0 || parseFloat(frequency_penalty) > 2) {
                        error_message = 'Please enter a valid frequency penalty value between 0 and 2';
                    } else if (presence_penalty === '') {
                        error_message = 'Please enter presence penalty';
                    } else if (parseFloat(presence_penalty) < 0 || parseFloat(presence_penalty) > 2) {
                        error_message = 'Please enter a valid presence penalty value between 0 and 2';
                    }
                    if (error_message) {
                        alert(error_message);
                    } else {
                        if (typeof wpaicgFormData.fields === 'object') {
                            for (var i = 0; i < wpaicgFormData.fields.length; i++) {
                                var form_field = wpaicgFormData.fields[i];
                                var field = wpaicgForm.getElementsByClassName('wpaicg-form-field-' + i)[0];
                                var field_type = form_field['type'] !== undefined ? form_field['type'] : 'text';
                                var field_label = form_field['label'] !== undefined ? form_field['label'] : '';
                                var field_min = form_field['min'] !== undefined ? form_field['min'] : '';
                                var field_max = form_field['max'] !== undefined ? form_field['max'] : '';
                                if (field_type !== 'radio' && field_type !== 'checkbox') {
                                    var field_value = field.value;
                                    if (field_type === 'text' || field_type === 'textarea' || field_type === 'email' || field_type === 'url') {
                                        if (field_min !== '' && field_value.length < parseInt(field_min)) {
                                            error_message = field_label + ' minimum ' + field_min + ' characters';
                                        } else if (field_max !== '' && field_value.length > parseInt(field_max)) {
                                            error_message = field_label + ' maximum ' + field_max + ' characters';
                                        } else if (field_type === 'email' && !wpaicg_PlayGround.validate.email(field_value)) {
                                            error_message = field_label + ' must be email address';
                                        } else if (field_type === 'url' && !wpaicg_PlayGround.validate.url(field_value)) {
                                            error_message = field_label + ' must be url';
                                        }
                                    } else if (field_type === 'number') {
                                        if (field_min !== '' && parseFloat(field_value) < parseInt(field_min)) {
                                            error_message = field_label + ' minimum ' + field_min;
                                        } else if (field_max !== '' && parseFloat(field_value) > parseInt(field_max)) {
                                            error_message = field_label + ' maximum ' + field_max;
                                        }
                                    }
                                } else if (field_type === 'checkbox' || field_type === 'radio') {
                                    var field_inputs = field.getElementsByTagName('input');
                                    var field_checked = false;
                                    if (field_inputs && field_inputs.length) {
                                        for (var y = 0; y < field_inputs.length; y++) {
                                            var field_input = field_inputs[y];
                                            if (field_input.checked) {
                                                field_checked = true;
                                            }
                                        }
                                    }
                                    if (!field_checked) {
                                        error_message = field_label + ' is required';
                                    }
                                }
                            }
                        }
                        if(error_message){
                            alert(error_message);
                        }
                        else{
                            if (typeof wpaicgFormData.fields === 'object') {
                                for (var i = 0; i < wpaicgFormData.fields.length; i++) {
                                    var form_field = wpaicgFormData.fields[i];
                                    var field_type = form_field.type;
                                    var field = wpaicgForm.getElementsByClassName('wpaicg-form-field-' + i)[0];
                                    var field_name = form_field['id'] !== undefined ? form_field['id'] : '';
                                    var field_value;
                                    if (field_type === 'checkbox' || field_type === 'radio') {
                                        field_value = '';
                                        var field_inputs = field.getElementsByTagName('input');
                                        if (field_inputs && field_inputs.length) {
                                            for (var y = 0; y < field_inputs.length; y++) {
                                                var field_input = field_inputs[y];
                                                if (field_input.checked) {
                                                    var current_field_value = field_input.value;
                                                    if (current_field_value !== undefined && current_field_value !== '') {
                                                        field_value += (field_value === '' ? '' : ', ') + current_field_value;
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        field_value = field.value;
                                    }
                                    var sRegExInput = new RegExp('{' + field_name + '}', 'g');
                                    title = title.replace(sRegExInput, field_value);
                                }
                            }
                            if(formSource === 'form') {
                                wpaicgPromptTitleFilled.value = title + ".\n\n";
                            }
                            let queryString = new URLSearchParams(new FormData(wpaicgForm)).toString();
                            wpaicg_PlayGround.loading.add(wpaicgGenerateBtn);
                            wpaicgSaveResult.style.display = 'none';
                            wpaicgStop.style.display = 'inline';
                            wpaicg_PlayGround.setContent(wpaicgFormData.response,formID,'');
                            queryString += '&source_stream='+formSource+'&nonce='+wpaicgFormData.ajax_nonce;
                            var eventID = Math.ceil(Math.random()*1000000);
                            wpaicgStop.setAttribute('data-event',eventID);
                            window['eventGenerator'+eventID] = new EventSource(wpaicgFormData.event + '&' + queryString);
                            if(formSource === 'form'){
                                queryString += '&action=wpaicg_form_log';
                            }
                            else{
                                queryString += '&action=wpaicg_prompt_log';
                            }
                            wpaicg_PlayGround.process(queryString,eventID,wpaicgFormData,formID,wpaicgStop,wpaicgSaveResult,wpaicgGenerateBtn,wpaicgMaxLines);
                        }
                    }
                })
            }
        }
    },
    process: function(queryString,eventID,wpaicgFormData,formID,wpaicgStop,wpaicgSaveResult,wpaicgGenerateBtn,wpaicgMaxLines){
        var wpaicg_PlayGround = this;
        var wpaicg_break_newline = wpaicgUserLoggedIn ? '<br/><br />': '\n\n';
        var startTime = new Date();
        var wpaicg_response_events = 0;
        var wpaicg_newline_before = false;
        var prompt_response = '';
        var wpaicg_limited_token = false;
        var count_line = 0;
        var wpaicg_limitLines = parseFloat(wpaicgMaxLines.value);
        var currentContent = '';
        window['eventGenerator'+eventID].onmessage = function (e) {
            currentContent = wpaicg_PlayGround.getContent(wpaicgFormData.response,formID);
            if (e.data === "[DONE]") {
                count_line += 1;
                wpaicg_PlayGround.setContent(wpaicgFormData.response,formID,currentContent + wpaicg_break_newline);
                wpaicg_response_events = 0;
            }
            else if (e.data === "[LIMITED]") {
                wpaicg_limited_token = true;
                count_line += 1;
                wpaicg_PlayGround.setContent(wpaicgFormData.response,formID,currentContent + wpaicg_break_newline);
                wpaicg_response_events = 0;
            } else {
                var result = JSON.parse(e.data);
                var content_generated = '';
                if (result.error !== undefined) {
                    content_generated = result.error.message;
                } else {
                    content_generated = result.choices[0].delta !== undefined ? (result.choices[0].delta.content !== undefined ? result.choices[0].delta.content : '') : result.choices[0].text;
                }
                prompt_response += content_generated;
                if ((content_generated === '\n' || content_generated === ' \n' || content_generated === '.\n' || content_generated === '\n\n') && wpaicg_response_events > 0 && currentContent !== '') {
                    if (!wpaicg_newline_before) {
                        wpaicg_newline_before = true;
                        wpaicg_PlayGround.setContent(wpaicgFormData.response,formID,currentContent + wpaicg_break_newline);
                    }
                }
                else if(content_generated.indexOf("\n") > -1 && wpaicg_response_events > 0 && currentContent !== ''){
                    if (!wpaicg_newline_before) {
                        wpaicg_newline_before = true;
                        if(wpaicgFormData.response === 'textarea'){
                            if(!wpaicg_PlayGround.editor(formID)){
                                content_generated = content_generated.replace(/\n/g,'<br><br>');
                            }
                        }
                        else{
                            content_generated = content_generated.replace(/\n/g,'<br><br>');
                        }
                        wpaicg_PlayGround.setContent(wpaicgFormData.response,formID,currentContent + content_generated);
                    }
                }
                else if (content_generated === '\n' && wpaicg_response_events === 0 && currentContent === '') {

                } else {
                    wpaicg_newline_before = false;
                    wpaicg_response_events += 1;
                    wpaicg_PlayGround.setContent(wpaicgFormData.response,formID,currentContent + content_generated);
                }
            }
            if (count_line === wpaicg_limitLines) {
                if(!wpaicg_limited_token) {
                    let endTime = new Date();
                    let timeDiff = endTime - startTime;
                    timeDiff = timeDiff / 1000;
                    queryString += '&prompt_id=' + wpaicgFormData.id + '&prompt_name=' + wpaicgFormData.name + '&prompt_response=' + prompt_response + '&duration=' + timeDiff + '&_wpnonce=' + wpaicgFormData.nonce + '&source_id=' + wpaicgFormData.sourceID;
                    const xhttp = new XMLHttpRequest();
                    xhttp.open('POST', wpaicgFormData.ajax);
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send(queryString);
                    xhttp.onreadystatechange = function (oEvent) {
                        if (xhttp.readyState === 4) {

                        }
                    }
                }
                wpaicg_PlayGround.eventClose(eventID,wpaicgStop,wpaicgSaveResult,wpaicgGenerateBtn,wpaicg_limited_token);
            }
        }
    },
    editor: function (form_id){
        var basicEditor = true;
        if(wpaicg_prompt_logged){
            var editor = tinyMCE.get('wpaicg-prompt-result-'+form_id);
            if ( document.getElementById('wp-wpaicg-prompt-result-'+form_id+'-wrap').classList.contains('tmce-active') && editor ) {
                basicEditor = false;
            }
        }
        return basicEditor;
    },
    setContent: function (type,form_id,value){
        if(type === 'textarea') {
            if (this.editor(form_id)) {
                document.getElementById('wpaicg-prompt-result-'+form_id).value = value;
            } else {
                var editor = tinyMCE.get('wpaicg-prompt-result-'+form_id);
                editor.setContent(value);
            }
        }
        else{
            document.getElementById('wpaicg-prompt-result-'+form_id).innerHTML = value;
        }
    },
    getContent: function (type,form_id){
        if(type === 'textarea') {
            if (this.editor(form_id)) {
                return document.getElementById('wpaicg-prompt-result-'+form_id).value
            } else {
                var editor = tinyMCE.get('wpaicg-prompt-result-'+form_id);
                var content = editor.getContent();
                content = content.replace(/<\/?p(>|$)/g, "");
                return content;
            }
        }
        else return document.getElementById('wpaicg-prompt-result-'+form_id).innerHTML;
    },
    loading: {
        add: function (btn){
            btn.setAttribute('disabled','disabled');
            btn.innerHTML += '<span class="wpaicg-loader"></span>';
        },
        remove: function (btn){
            btn.removeAttribute('disabled');
            btn.removeChild(btn.getElementsByTagName('span')[0]);
        }
    },
    eventClose: function (eventID,btn,btnResult,btn_generator,wpaicg_limited_token){
        btn.style.display = 'none';
        if(!wpaicg_limited_token) {
            btnResult.style.display = 'block';
        }
        this.loading.remove(btn_generator);
        window['eventGenerator'+eventID].close();
    },
    validate: {
        email: function (email){
            return String(email)
                .toLowerCase()
                .match(
                    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
        },
        url: function (url){
            try {
                new URL(string);
                return true;
            } catch (err) {
                return false;
            }
        }
    }
}
wpaicgPlayGround.init();
