// JavaScript Document
function encodeData(data) {
    var is_encrypted = true;

    if (!is_encrypted) return data;

    var obj = {};

    var regExpr = /[^a-zA-Z0-9-._^,@$$ ]/g;

    data = data.split('&');
    for (var i = 0; i < data.length; i++) {
        data[i] = data[i].split('=');
        obj[data[i][0]] = data[i][1];
    }


    var key = CryptoJS.enc.Hex.parse('bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3');
    var iv = CryptoJS.enc.Hex.parse('101112131415161718191a1b1c1d1e1f');

    var encrypted = CryptoJS.AES.encrypt(JSON.stringify(obj), key, { iv: iv });

    var password_base64 = encrypted.ciphertext.toString(CryptoJS.enc.Base64);

    var password_base64_URI = encodeURIComponent(password_base64);

    return '0=' + password_base64_URI;
}


// JavaScript Document
function setState(id, url, datas) {
    console.log("data=" + datas);
    //	console.log("encrypted data="+encodeData(datas));
    id = '#' + id;
    rul = url;
    $htmlObj = $.ajax({
        type: "POST",
        url: rul,
        ifModified: true,
        dataType: "html",
        data: datas,
        cache: false,
        headers: {
            "X-Authentication": $('#sT').text(),
            "X-Access-Token": $('#aT').text()
        },
        beforeSend: function() {
            // Show your spinner
            NProgress.start();

        },
        success: function(result) {
            NProgress.done();

            $(id).html(result);

        },
        error: function() {
            NProgress.done();
            $(id).html("401 Unauthorized Request");
            //setStateGet(id,url,datas);
        }


    });
}


function setStateGet(id, url, datas) {
    //console.log("data="+datas);
    //	console.log("encrypted data="+encodeData(datas));
    id = '#' + id;
    rul = url;
    $htmlObj = $.ajax({
        type: "GET",
        url: rul,
        ifModified: true,
        dataType: "html",
        data: datas,
        cache: false,
        headers: {
            "X-Authentication": $('#sT').text(),
            "X-Access-Token": $('#aT').text()
        },

        beforeSend: function() {
            // Show your spinner
            NProgress.start();
            //   $('#formLoader').fadeIn();	
        },
        success: function(result) {
            NProgress.done();

            $(id).html(result);
        },
        error: function() {
            NProgress.done();
            $(id).html("401 Unauthorized Request");
            //setStateGet(id,url,datas);
        }
    });
}

function setStateVal(id, url, datas) {
    id = '#' + id;
    rul = url;
    $htmlObj = $.ajax({
        type: "GET",
        url: rul,
        ifModified: true,
        dataType: "text",
        data: datas,
        cache: false,
        beforeSend: function() {
            // Show your spinner
            NProgress.start();
            //   $('#formLoader').fadeIn();	
        },
        success: function(result) {
            NProgress.done();
            //    $('#formLoader').fadeOut();
            $(id).val(result);
        }
    });
}


function confirmDelete(id, path, datas) {

    if (confirm("Are you sure You want to delete this?")) {
        setStateGet(id, path, datas);
    }
}


//Form Animations
function animateForm(url, formData, tableData) {

    $('.panel-body .btn-success').hide();

    $("html:not(:animated),body:not(:animated)").animate({ scrollTop: 0 }, 1000);

    setTimeout(function() {

        $('#adminForm').effect('fadeIn', 350, function() {

            $(this).slideUp(200, function() {

                setStateGet('adminForm', url, formData);

            });

        });
    }, 900);

    $('.panel-body .btn-success').fadeIn(2000);


    setStateGet('adminTable', url, tableData);

}

function countUp(count, id, div_by, cnt_speed) {
    var speed = Math.round(count / div_by),
        $display = $(id),
        run_count = 1,
        int_speed = cnt_speed;

    var int = setInterval(function() {
        if (run_count < div_by) {
            $display.text(speed * run_count);
            run_count++;
        } else if (parseInt($display.text()) < count) {
            var curr_count = parseInt($display.text()) + 1;
            $display.text(curr_count);
        } else {
            clearInterval(int);
        }
    }, int_speed);
}

function navigate(rul, id) {


    $('#navigation a').removeClass('active');
    $('#' + id).addClass('active');

    $("#loading").css('display', 'inline');

    //$("#l_bar").css('display','inline');

    $('#content').slideUp("1000", function() {

        htmlobj = $.ajax({
            url: rul,
            cache: false,
            async: false,
            dataType: "html",
            timeout: 4000,
            ifModified: true,


            error: function(objAJAXRequest, strError, strError1) {
                alert(strError1);
            }


        });


        $('#content').html(htmlobj.responseText);

        $('#content').slideDown("3000");
        // $("#l_bar").css('display','none');
        $("#loading").css('display', 'none');

    });

    //setStateGet('rightDiv',url,'displayPage=1')
    //	alert($('#'+id).text());

}


function addslashes(str) {

    str = encodeURIComponent(str);


    return (str + '')
        .replace(/[\\"']/g, '\\$&')
        .replace(/\u0000/g, '\\0');
}



// American Numbering System
var th = ['', 'thousand', 'million', 'billion', 'trillion'];
// uncomment this line for English Number System
// var th = ['','thousand','million', 'milliard','billion'];

var dg = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
var tn = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
var tw = ['twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

function toWords(s) { s = s.toString();
    s = s.replace(/[\, ]/g, ''); if (s != String(parseFloat(s))) return 'not a number'; var x = s.indexOf('.'); if (x == -1) x = s.length; if (x > 15) return 'too big'; var n = s.split(''); var str = ''; var sk = 0; for (var i = 0; i < x; i++) { if ((x - i) % 3 == 2) { if (n[i] == '1') { str += tn[Number(n[i + 1])] + ' ';
                i++;
                sk = 1; } else if (n[i] != 0) { str += tw[n[i] - 2] + ' ';
                sk = 1; } } else if (n[i] != 0) { str += dg[n[i]] + ' '; if ((x - i) % 3 == 0) str += 'hundred ';
            sk = 1; } if ((x - i) % 3 == 1) { if (sk) str += th[(x - i - 1) / 3] + ' ';
            sk = 0; } } if (x != s.length) { var y = s.length;
        str += 'point '; for (var i = x + 1; i < y; i++) str += dg[n[i]] + ' '; } return str.replace(/\s+/g, ' '); }