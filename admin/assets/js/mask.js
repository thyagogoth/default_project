function mascara(o, f) {
	v_obj = o
	v_fun = f
	setTimeout("execmascara()", 1)
}
function execmascara() {
	v_obj.value = v_fun(v_obj.value);
}
function mcep(v) {
	v = v.replace(/\D/g, "")                    //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/^(\d{5})(\d)/, "$1-$2")         //Esse ï¿½ tï¿½o fï¿½cil que nï¿½o merece explicaï¿½ï¿½es
	return v
}
function mtel(v) {
	v = v.replace(/\D/g, "");             //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/^(\d{2})(\d)/g, "($1) $2"); //Coloca parï¿½nteses em volta dos dois primeiros dï¿½gitos
	v = v.replace(/(\d)(\d{4})$/, "$1-$2");    //Coloca hï¿½fen entre o quarto e o quinto dï¿½gitos
	return v;
}
function mcnpj(v) {
	v = v.replace(/\D/g, "")                           //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/^(\d{2})(\d)/, "$1.$2")             //Coloca ponto entre o segundo e o terceiro dï¿½gitos
	v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3") //Coloca ponto entre o quinto e o sexto dï¿½gitos
	v = v.replace(/\.(\d{3})(\d)/, ".$1/$2")           //Coloca uma barra entre o oitavo e o nono dï¿½gitos
	v = v.replace(/(\d{4})(\d)/, "$1-$2")              //Coloca um hï¿½fen depois do bloco de quatro dï¿½gitos
	return v
}
function mcpf(v) {
	v = v.replace(/\D/g, "")                    //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/(\d{3})(\d)/, "$1.$2")       //Coloca um ponto entre o terceiro e o quarto dï¿½gitos
	v = v.replace(/(\d{3})(\d)/, "$1.$2")       //Coloca um ponto entre o terceiro e o quarto dï¿½gitos
	//de novo (para o segundo bloco de nï¿½meros)
	v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2") //Coloca um hï¿½fen entre o terceiro e o quarto dï¿½gitos
	return v
}
function mdata(v) {
	v = v.replace(/\D/g, "");                    //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/(\d{2})(\d)/, "$1/$2");
	v = v.replace(/(\d{2})(\d)/, "$1/$2");
	v = v.replace(/(\d{2})(\d{2})$/, "$1$2");
	return v;
}
function mtempo(v) {
	v = v.replace(/\D/g, "");                    //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/(\d{1})(\d{2})(\d{2})/, "$1:$2.$3");
	return v;
}
function mhora(v) {
	v = v.replace(/\D/g, "");                    //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/(\d{2})(\d)/, "$1h$2");
	return v;
}
function dois_pontos(tempo){
	if(event.keyCode<48 || event.keyCode>57){
		event.returnValue=false;
	}
	if(tempo.value.length==2 || tempo.value.length==5) {
		tempo.value+=":";
	}
}
function valida_horas(tempo){
	horario = tempo.value.split("h");
	var horas = parseInt(horario[0]);
	var minutos = parseInt(horario[1]);
	if( horas > 24 || minutos > 59 ){
		alert("Horï¿½rio informado invï¿½lido");
		//event.returnValue=false;
		//document.getElementById(tempo.id).focus();
		document.getElementById(tempo.id).value = '';
	}
}
function mrg(v) {
	v = v.replace(/\D/g, "");                                      //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/(\d)(\d{7})$/, "$1.$2");    //Coloca o . antes dos ï¿½ltimos 3 dï¿½gitos, e antes do verificador
	v = v.replace(/(\d)(\d{4})$/, "$1.$2");    //Coloca o . antes dos ï¿½ltimos 3 dï¿½gitos, e antes do verificador
	v = v.replace(/(\d)(\d)$/, "$1-$2");               //Coloca o - antes do ï¿½ltimo dï¿½gito
	return v;
}
function mnum(v) {
	v = v.replace(/\D/g, "");                                      //Remove tudo o que nï¿½o ï¿½ dï¿½gito
	return v;
}
function mvalor(v) {
	v = v.replace(/\D/g, "");//Remove tudo o que nï¿½o ï¿½ dï¿½gito
	v = v.replace(/(\d)(\d{8})$/, "$1.$2");//coloca o ponto dos milhï¿½es
	v = v.replace(/(\d)(\d{5})$/, "$1.$2");//coloca o ponto dos milhares
	v = v.replace(/(\d)(\d{2})$/, "$1,$2");//coloca a virgula antes dos 2 ï¿½ltimos dï¿½gitos
	return v;
}
function vCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g,'');
    if(cpf == '') return false;
    // Elimina CPFs invalidos conhecidos
    if (cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999")
        return false;
    // Valida 1o digito
    add = 0;
    for (i=0; i < 9; i ++)
        add += parseInt(cpf.charAt(i)) * (10 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(9)))
        return false;
    // Valida 2o digito
    add = 0;
    for (i = 0; i < 10; i ++)
        add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(10)))
        return false;
    return true;
}
