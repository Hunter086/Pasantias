/*document.getElementById("Select").onclick = function() {clickFunction(idinput, idSelect)};
function clickFunction(idinput, idSelect) {
    $("option").click(function(){
        selector = document.getElementById(idSelect);
        option = selector.getElementsByTagName("option");
        idinput.value= (selector.options[selector.selectedIndex].text);
    }); 

}*/
function filterFunction(idinput, idSelect) {
    var input, filter, ul, li, a, i;
    input = document.getElementById(idinput);
    filter = input.value;
    selector = document.getElementById(idSelect);
    option = selector.getElementsByTagName("option");
    if (filter==""){
        for (i = 0; i < option.length; i++) {
            txtValue = option[i].text;
            if(option[i].value==""){
                option[i].setAttribute('selected', 'selected');
                option[i].style.display="";
            }else{
                option[i].style.display="";
                option[i].removeAttribute("selected");
            }
        }
    }else{
        for (i = 0; i < option.length; i++) {
        txtValue = option[i].text;
            if (txtValue.indexOf(filter) == 0) {
                option[i].style.display="";
                option[i].setAttribute('selected', 'selected');
            } else {
                option[i].removeAttribute("selected");
                option[i].style.display="none";
            }
        }
    }
    
    } 