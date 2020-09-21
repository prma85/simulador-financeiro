/*=============================================================
    Authour URI: www.uregina.ca/~martinsp/
    License: Commons Attribution 3.0

    http://creativecommons.org/licenses/by/3.0/

    100% Free To use For Personal And Commercial Use.
    IN EXCHANGE JUST GIVE US CREDITS AND TELL YOUR FRIENDS ABOUT IT
   
    ========================================================  */


// How to use: put the following code on the input that you want the mask
// onkeypress="mask(this,function_name)"

function mask(o, f) {
    v_obj = o
    v_fun = f
    setTimeout("execMask()", 1)
}
function execMask() {
    v_obj.value = v_fun(v_obj.value)
}
function justLetters(v) {
    return v.replace(/\d/g, "") //Remove tudo o que não é Letra
}
function justLettersUP(v) {
    v = v.toUpperCase() //Maiúsculas
    return v.replace(/\d/g, "") //Remove tudo o que não é Letra ->maiusculas
}
function justLettersLO(v) {
    v = v.toLowerCase() //Minusculas
    return v.replace(/\d/g, "") //Remove tudo o que não é Letra ->minusculas
}
function justNumbers(v) {
    return v.replace(/\D/g, "") //Remove tudo o que não é dígito
}
function phone(v) {
    var r = v.replace(/\D/g,"");
    r = r.replace(/^0/,"");
    if (r.length > 5) {
        // 6+ digits. Format as 3+4
        r = r.replace(/^(\d\d\d)(\d{3})(\d{0,4}).*/,"($1) $2-$3");
    }
    else if (r.length > 3) {
        // 4..6 digits. Add (XXX)...
        r = r.replace(/^(\d\d\d)(\d{0,5})/,"($1) $2");
    }
    else {
        // 0..3 digits. Just add (XXX)
        r = r.replace(/^(\d*)/, "($1)");
    }
    return r;
}

	

//Social Security Number for CANADA
 function sinCanada(v) {
    var r = v.replace(/\D/g,"");
    r = r.replace(/^0/,"");
    if (r.length > 9) {
        r = r.replace(/^(\d\d\d)(\d{3})(\d{0,3}).*/,"$1-$2-$3");
        return r;
    }
    else if (r.length > 4) {
        r = r.replace(/^(\d\d\d)(\d{3})(\d{0,3}).*/,"$1-$2-$3");
    }
    else if (r.length > 2) {
        r = r.replace(/^(\d\d\d)(\d{0,3})/,"$1-$2");
    }
    else {
        r = r.replace(/^(\d*)/, "$1");
    }
    return r;
}
//Social Security Number for USA
 function sinUSA(v) {
    var r = v.replace(/\D/g,"");
    r = r.replace(/^0/,"");
    if (r.length > 9) {
        r = r.replace(/^(\d\d\d)(\d{2})(\d{0,4}).*/,"$1-$2-$3");
        return r;
    }
    else if (r.length > 4) {
        r = r.replace(/^(\d\d\d)(\d{2})(\d{0,4}).*/,"$1-$2-$3");
    }
    else if (r.length > 2) {
        r = r.replace(/^(\d\d\d)(\d{0,3})/,"$1-$2");
    }
    else {
        r = r.replace(/^(\d*)/, "$1");
    }
    return r;
}

//USA date
function mdate(v) {
   var r = v.replace(/\D/g,"");
   if (r.length > 4) {
    r = r.replace(/^(\d\d)(\d{2})(\d{0,4}).*/,"$1/$2/$3");
   }
   else if (r.length > 2) {
    r = r.replace(/^(\d\d)(\d{0,2})/,"$1/$2");
   }
   else if (r.length > 0){
         if (r > 12) {
           r = "";
         }
   }
   return r;
}

// MASK FOR CREDIT CARDS
/* how to use
var s1 = "4567 6365 7987 3783";
var s2 = "3457 732837 82372";
console.log(ccStarry(s1));
console.log(ccStarry(s2));

result
4567 **** **** 3783
3457 ****** 82372

 */
function starry(match, gr1, gr2, gr3) {
  var stars = gr2.replace(/\d/g, '*');
  return gr1 + " " + stars + " " + gr3;
}

function ccStarry(str) {
  var rex = /(\d{4})\s(\d{4}\s\d{4}|\d{6})\s(\d{4}|\d{5})/;

  if (rex.test(str))
    return str.replace(rex, starry);
  else return "";
}
