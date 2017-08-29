/**
 * Created by juanse254 on 7/14/17.
 */

jQuery(document).ready(function() {
    var isshow = localStorage.getItem('isshow');
    if (isshow == null) { //TODO: esto va == en vez de !=
        localStorage.setItem('isshow', 1);
        // Show popup here
        Popup();
        jQuery("#DialogUI").dialog({
            minWidth: 450,
            maxWidth: 600,
            resizable: false,
            modal: true,
            draggable: false
        });
    }
});

function Popup() {

    var node = document.getElementById('wrapper');
    var newNode = document.createElement('div'); // contenedor
    newNode.setAttribute("id", "DialogUI");

    var headerDiv = document.createElement('div'); //header
    headerDiv.setAttribute("id", "header-dialog");

    var h2Header = document.createElement('p'); //Texto Header
    h2Header.appendChild(document.createTextNode('Hi there! we are offering'));
    var h1Header = document.createElement('h3');
    h1Header.appendChild(document.createTextNode('15% off on the first order with the CODE bla'));
    var h2Header2 = document.createElement('p');
    h2Header2.appendChild(document.createTextNode('And 3 day free shipping on all orders!'));
    var imgHeader = document.createElement('img');
    imgHeader.setAttribute("src","/wp-content/uploads/2017/07/imgpsh_fullsize-1.png");
    imgHeader.setAttribute("class","img-responsive");
    imgHeader.setAttribute("alt","Promo 15% off");
    imgHeader.setAttribute("style","margin: auto; max-height:300px");
   // headerDiv.appendChild(h2Header);
   // headerDiv.appendChild(h1Header);
   // headerDiv.appendChild(h2Header2);
    headerDiv.appendChild(imgHeader);

    var bodyDiv = document.createElement('div'); //body
    bodyDiv.setAttribute("id", "body-dialog");

    var form = document.createElement('form'); // register form
        form.setAttribute("method", "post");
        form.setAttribute("class", "register");
        //var p1Register = document.createElement('a');
        // var p1form = document.createElement('p');
        //     p1form.setAttribute("class","woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide");
        //     var label1p1form = document.createElement('label');
        //         label1p1form.setAttribute("for", "reg_email");
        //         label1p1form.appendChild(document.createTextNode('Email'));
        //     var inputp1form = document.createElement('input');
        //         inputp1form.setAttribute("type", "email");
        //         inputp1form.setAttribute("class", "woocommerce-Input woocommerce-Input--text input-text");
        //         inputp1form.setAttribute("name", "email");
        //         inputp1form.setAttribute("id", "reg_email");
        //  p1form.appendChild(label1p1form);
        //  p1form.appendChild(inputp1form);
        // var p2form = document.createElement('p');
        //     p2form.setAttribute("class","woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide");
        //     var label1p2form = document.createElement('label');
        //         label1p2form.setAttribute("for", "reg_password");
        //         label1p2form.appendChild(document.createTextNode('Password'));
        //     var inputp2form = document.createElement('input');
        //         inputp2form.setAttribute("type", "password");
        //         inputp2form.setAttribute("class", "woocommerce-Input woocommerce-Input--text input-text");
        //         inputp2form.setAttribute("name", "password");
        //         inputp2form.setAttribute("id", "reg_password");
        // p2form.appendChild(label1p2form);
        //p2form.appendChild(inputp2form);

        var register_button = document.createElement('p');
            register_button.setAttribute("class", "woocomerce-FormRow form-row");
            var inputregister1 = document.createElement('input');
                inputregister1.setAttribute("type","input");
		        inputregister1.setAttribute("id","register_first_time");
                inputregister1.setAttribute("class","woocommerce-Button button");
                inputregister1.setAttribute("name","register");
		inputregister1.setAttribute("style","margin-left: 15%");
                inputregister1.setAttribute("value","Register");
                inputregister1.setAttribute("onclick","location.href='/my-account/'");
            register_button.appendChild(inputregister1);

    //form.appendChild(p1form);
    //form.appendChild(p2form);
    form.appendChild(register_button);

    var social_login = document.createElement('div');
        social_login.setAttribute("class","page-title-inner flex-row  container");
        var social_login_innerdiv = document.createElement('div');
            social_login_innerdiv.setAttribute("class", "flex-col flex-grow medium-text-center");
            var div_inside_social = document.createElement('div');
                div_inside_social.setAttribute("class", "text-center social-login");
                var facebook_atrr = document.createElement('a');
                    facebook_atrr.setAttribute("href","http://magnaflor.com/wp-login.php?loginFacebook=1&redirect=http://magnaflor.com/"); //TODO actualizar url
                    facebook_atrr.setAttribute("class", "button social-button large facebook circle");
                    facebook_atrr.setAttribute("onclick", "window.location = 'http://magnaflor.com/wp-login.php?loginFacebook=1&redirect='+window.location.href; return false;");
                    facebook_atrr.setAttribute("style","margin-bottom: 5px");
                        var facebook_icon = document.createElement('i');
                            facebook_icon.setAttribute("class", "icon-facebook");
                        var facebook_span = document.createElement('span');
                            facebook_span.appendChild(document.createTextNode("Login with facebook"));
                    facebook_atrr.appendChild(facebook_icon);
                    facebook_atrr.appendChild(facebook_span);
                var google_atrr = document.createElement('a');
                    google_atrr.setAttribute("href","http://magnaflor.com/wp-login.php?loginGoogle=1&redirect=http://magnaflor.com/"); //TODO actualizar url
                    google_atrr.setAttribute("class", "button social-button large google-plus circle");
                    google_atrr.setAttribute("onclick", "window.location = 'http://magnaflor.com/wp-login.php?loginGoogle=1&redirect='+window.location.href; return false;");
                        var google_icon = document.createElement('i');
                            google_icon.setAttribute("class", "icon-google-plus");
                        var google_span = document.createElement('span');
                            google_span.appendChild(document.createTextNode("Login with google"));
                    google_atrr.appendChild(google_icon);
                    google_atrr.appendChild(google_span);
            div_inside_social.appendChild(facebook_atrr);
            div_inside_social.appendChild(google_atrr);
        social_login_innerdiv.appendChild(div_inside_social);
    social_login.appendChild(social_login_innerdiv);


    bodyDiv.appendChild(form);
    bodyDiv.appendChild(social_login);

    newNode.appendChild(headerDiv);
    newNode.appendChild(bodyDiv);
    node.appendChild(newNode);
}
