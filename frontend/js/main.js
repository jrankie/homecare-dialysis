const button = document.getElementById("calcular")
button.addEventListener("click",function(){
    event.preventDefault()
    let drenaje1 = document.getElementById("drenar1").value
    let balance1 = 2000 - drenaje1
    document . getElementById("balance1").innerHTML = balance1

    let drenaje2 = document.getElementById("drenar2").value
    let balance2 = 2000 - drenaje2
    document . getElementById("balance2").innerHTML = balance2

    let drenaje3 = document.getElementById("drenar3").value
    let balance3 = 2000 - drenaje3
    document . getElementById("balance3").innerHTML = balance3

    let drenaje4 = document.getElementById("drenar4").value
    let balance4 = 2000 - drenaje4
    document . getElementById("balance4").innerHTML = balance4



    let totalDrenaje = Number(drenaje1) + Number(drenaje2) + Number(drenaje3) + Number(drenaje4)
    let totalInfusion = 2000 * 4
    let totalBalance = totalInfusion - totalDrenaje

    document.getElementById("totalDrenaje").innerHTML = totalDrenaje
    document.getElementById("totalInfusion").innerHTML = totalInfusion
    document.getElementById("totalBalance").innerHTML = totalBalance


    let turbioCount = 0
    if(document.getElementById("cualidad1").value == "Turbio") turbioCount++
    if(document.getElementById("cualidad2").value == "Turbio") turbioCount++
    if(document.getElementById("cualidad3").value == "Turbio") turbioCount++
    if(document.getElementById("cualidad4").value == "Turbio") turbioCount++




    if(totalBalance <=0){
        document.getElementById("analisis").innerHTML="Balance Hídrico Favorable. Condición normal, no hay retención de líquidos."
    }else if(totalBalance >=1 && totalBalance <=2000){
        document.getElementById("analisis").innerHTML="Retención de líquidos considerable."
    }else{
        document.getElementById("analisis").innerHTML="ALERTA: Excesiva retención de líquidos."
        alert("ALERTA: Excesiva retención de líquidos.")
    }


    if(turbioCount >= 2){
        document.getElementById("analisis").innerHTML += "<br>ALERTA: Consulte de inmediato con su nefrólogo."
        alert("ALERTA: Consulte de inmediato con su nefrólogo.")
}


})