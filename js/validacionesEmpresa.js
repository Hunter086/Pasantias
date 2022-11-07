function validate() {
      
    if( document.myForm.Name.value == "" && document.myForm.Name.lenght<= 60 ) {
       alert( "Please provide your name!" );
       document.myForm.nombre.focus() ;
       return false;
    }
    return( true );
 }