    //const constraints = {video: true};

    const video = document.querySelector('video');

    const vgaConstraints = {
    //video: true,
    video: {width: {exact: 640}, height: {exact: 480}},
    audio: false
    };
    
    navigator.mediaDevices.getUserMedia(vgaConstraints).then((stream) => {video.srcObject = stream});
    
    const screenButton = document.querySelector('#screenbutton');
    const canvas = document.createElement('canvas');

    var img2 = "";
    var prev = document.querySelector('#preimg');

    document.querySelector('#thug').onclick = function() {
        img2 = 'src/pics/thug.png';
        prev.src = img2;
        screenButton.classList.remove('unclick');
        button.classList.remove('unclick');
    }
    document.querySelector('#ahshit').onclick = function() {
        img2 = 'src/pics/ahshit.png';
        prev.src = img2;
        screenButton.classList.remove('unclick');
        button.classList.remove('unclick');
    }
    document.querySelector('#morocco').onclick = function() {
        img2 = 'src/pics/morocco.png';
        prev.src = img2;
        screenButton.classList.remove('unclick');
        button.classList.remove('unclick');
    }
    document.querySelector('#wanted').onclick = function() {
        img2 = 'src/pics/wanted.png';
        prev.src = img2;
        screenButton.classList.remove('unclick');
        button.classList.remove('unclick');
    }
    //img2.src = '/src/pics/snoop.png';
    const upload = document.querySelector('#dropimg');
    var button = document.createElement("button");
    button.classList.add('unclick');
    button.innerHTML = "Take Picture";
    var uploadedimg = new Image();
    
    function fileCheck(fileInput) {
        // check file size for empty files
        if (fileInput.size == 0){
            alert('file uploaded is not allowed!');
            window.location.reload(false);
        }
        // check file format
        if (!fileInput.name.match(/.(jpg|jpeg|png|gif|bmp|tiff)$/i)){
            alert('file uploaded is not allowed!');
            window.location.reload(false);
        }
    }
    function onerrorfile() {
        window.location.reload(false);
    }
    upload.onchange = function () {
        //const stream = document.querySelector('#stream');
        const videol = document.querySelector('#video');
        const takel = document.querySelector('#takepic');
        video.style.display = 'none';
        screenButton.style.display = 'none';
        // creat button
        var file = document.querySelector('input[type=file]').files[0];
        fileCheck(file);
        var reader = new FileReader();
        reader.onloadend = function () {
            uploadedimg.src = reader.result;
            uploadedimg.setAttribute('onerror', 'onerrorfile();');
            takel.appendChild(button);
            //console.log(uploadedimg);
            videol.appendChild(uploadedimg);
            //stream.insertBefore(button, stream.childNodes[0]);
            //stream.insertBefore(uploadedimg, stream.childNodes[0]);
        }
        if (file){
            reader.readAsDataURL(file);
        }else{
            uploadedimg.src = "";
        }
    }

    // check if mirror id checked
    const mirror = document.querySelector('#mirror');
    mirror.onclick = function () {
        if (mirror.checked){
            video.classList.add("mirrored");
            uploadedimg.classList.add("mirrored");
            prev.classList.add("mirrored");
        }else{
            video.classList.remove("mirrored");
            uploadedimg.classList.remove("mirrored");
            prev.classList.remove("mirrored");
        }
    }

    // button for uploaded img
    button.onclick = function () {upimg(img2, canvas, uploadedimg);}
    // button for webcan
    screenButton.onclick = function () {camimg(img2, canvas, video);}

    function camimg(img2, canvas, video) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context = canvas.getContext('2d');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', "savepic.php",true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.responseText){
                const newimg = document.createElement('img');
                const newdiv = document.createElement('div');
                const newfrm = document.createElement("form");
                const hidden = document.createElement('input');
                const submit = document.createElement('input');
                const down = document.createElement('a');
                newdiv.className = "oldimg";
                newfrm.method = "POST";
                newfrm.action = "/home.php";
                hidden.type = "hidden";
                hidden.name = "img";
                hidden.value = xhr.responseText;
                submit.type = "submit";
                submit.name = "delete";
                submit.value = "DELETE";
                newimg.src = xhr.responseText;
                down.class = "down";
                down.href = xhr.responseText;
                down.setAttribute('download', xhr.responseText);
                var text = document.createTextNode("DOWNLOAD");
                down.appendChild(text);
                newdiv.appendChild(newfrm);
                newfrm.appendChild(newimg);
                newfrm.appendChild(hidden);
                newfrm.appendChild(submit);
                newfrm.appendChild(down);
                const div = document.querySelector('#imgs');
                const showimg = document.querySelector('#showimg');
                showimg.src = xhr.responseText;
                div.insertBefore(newdiv, div.childNodes[0]);
                //newimg.setAttribute('src', xhr.responseText);
                //div.insertBefore(newimg, div.childNodes[0]);
            }else{
                alert('Upload image or Allow access to your Camera');
            }
        }
        if (img2.length > 0){
            context.drawImage(video, 0, 0);
            var cimg = canvas.toDataURL('image/png');
            var onmg = encodeURIComponent(cimg);
            var body = "img=" + onmg + "&img2=" + img2;
            xhr.send(body);

            // marge 2 images using js
            /*
            context.globalAlpha = 1.0;
            context.drawImage(video, 0, 0);
            context.globalAlpha = 1.0;
            context.drawImage(img2, 0, 0);
            */
        }else{
            context.drawImage(video, 0, 0);
            var cimg = canvas.toDataURL('image/png');
            var onmg = encodeURIComponent(cimg);
            var body = "img=" + onmg;
            xhr.send(body);
            /* // using js
            context.drawImage(video, 0, 0);
            var cimg = canvas.toDataURL('image/png');
            */
        }
        // USING JS
        // Other browsers will fall back to image/png
        // creat image
        /*
        var pic = canvas.toDataURL('image/png');
        // save to img src
        img.src = pic;
        // create img element
        const newimg = document.createElement('img');
        newimg.setAttribute('src', img.src);

        //add photo to div
        const div = document.querySelector('#imgs');
        div.appendChild(newimg);
        // send to php
        console.log(pic);
        //data.append('file', pic);
        */
    }
    function upimg(img2, canvas, uploadedimg) {
        //checkvalidfile(uploadedimg);
        //console.log(uploadedimg.width, uploadedimg.height);
        canvas.width = uploadedimg.width;
        canvas.height = uploadedimg.height;
        context = canvas.getContext('2d');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', "savepic.php",true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.responseText){
                const newimg = document.createElement('img');
                const newdiv = document.createElement('div');
                const newfrm = document.createElement("form");
                const hidden = document.createElement('input');
                const submit = document.createElement('input');
                const down = document.createElement('a');
                newdiv.className = "oldimg";
                newfrm.method = "POST";
                newfrm.action = "/home.php";
                hidden.type = "hidden";
                hidden.name = "img";
                hidden.value = xhr.responseText;
                submit.type = "submit";
                submit.name = "delete";
                submit.value = "DELETE";
                newimg.src = xhr.responseText;
                down.class = "down";
                down.href = xhr.responseText;
                down.setAttribute('download', xhr.responseText);
                var text = document.createTextNode("DOWNLOAD");
                down.appendChild(text);
                newdiv.appendChild(newfrm);
                newfrm.appendChild(newimg);
                newfrm.appendChild(hidden);
                newfrm.appendChild(submit);
                newfrm.appendChild(down);
                const div = document.querySelector('#imgs');
                const showimg = document.querySelector('#showimg');
                showimg.src = xhr.responseText;
                div.insertBefore(newdiv, div.childNodes[0]);
            }else{
                alert('Upload image or Allow access to your Camera');
            }
        }
        if (img2.length > 0){
            context.drawImage(uploadedimg, 0, 0);
            var cimg = canvas.toDataURL('image/png');
            var onmg = encodeURIComponent(cimg);
            var body = "img=" + onmg + "&img2=" + img2;
            xhr.send(body);

        }else{
            context.drawImage(uploadedimg, 0, 0);
            var cimg = canvas.toDataURL('image/png');
            var onmg = encodeURIComponent(cimg);
            var body = "img=" + onmg;
            xhr.send(body);
        }
    }