$(document).ready(function () {
    $('.js-example-basic-single').select2();

    function convertirMilisegundosAHorasMinutosSegundos(ms) {
        let totalSegundos = Math.floor(ms / 1000);
        let horas = Math.floor(totalSegundos / 3600);
        let minutos = Math.floor((totalSegundos % 3600) / 60);
        let segundos = totalSegundos % 60;

        return [
            horas.toString().padStart(2, '0'),
            minutos.toString().padStart(2, '0'),
            segundos.toString().padStart(2, '0'),
        ].join(':');
    }

    function convertirMilisegundosALegible(ms) {
        let totalSegundos = Math.floor(ms / 1000);
        let horas = Math.floor(totalSegundos / 3600);
        let minutos = Math.floor((totalSegundos % 3600) / 60);
        let segundos = totalSegundos % 60;

        let partes = [];
        if (horas > 0) partes.push(horas + ' hora' + (horas !== 1 ? 's' : ''));
        if (minutos > 0) partes.push(minutos + ' minuto' + (minutos !== 1 ? 's' : ''));
        if (segundos > 0) partes.push(segundos + ' segundo' + (segundos !== 1 ? 's' : ''));

        return partes.length > 0 ? partes.join(' ') : '0 segundos';
    }

    // Oculta los demás campos si se selecciona "Otras Actividades" 
    $('#actividad').change(function () {
        let actividad = $(this).val();
        console.log(actividad);

        if (actividad === 'Otras Actividades') {
            // Oculta los campos no necesarios
            $('#grupo_incidencia, #grupo_tipo, #grupo_otros, #resultado_consulta').addClass('d-none');
            // Limpia y desactiva los campos asociados al select recinto
            $('#incidencia, #tipo, #recinto, #text_region, #text_ciudad, #text_nombre_gerente').val(null).prop('disabled', true);
            $('#incidencia, #tipo, #recinto, #text_region, #text_ciudad, #text_nombre_gerente').val('').prop('disabled', true);
        } else {
            // Muestra los campos (actividad != 'Otras Actividades')
            $('#grupo_incidencia, #grupo_tipo, #grupo_otros').removeClass('d-none');
            // Activa los campos nuevamente
            $('#incidencia, #tipo, #recinto, #text_region, #text_ciudad, #text_nombre_gerente').prop('disabled', false);
        }
    });

    // Lógica tipo = Call Center
    $('#tipo').change(function () {
        let tipo = $(this).val();
        console.log(`Tipo: ${tipo}`);

        if (tipo === 'Call Center') {
            // Limpia los valores
            $('#recinto').empty();
            // Agrega sólo los CC 
            $('#recinto').append(`
                <option value="" selected disabled>Selecciona un CC</option>
                <option value="CC Apodaca">CC Apodaca</option>
                <option value="CC Carmona y Valle">CC Carmona y Valle</option>
                <option value="CC Rio Mayo">CC Rio Mayo</option>
                <option value="CC Validacion">CC Validacion</option>
            `);

            // Oculta los campos no necesarios
            $('#grupo_region, #grupo_ciudad, #grupo_nombre_gerente').addClass('d-none');
            // Limpia y desactiva los campos asociados al select recinto
            $('#grupo_region, #grupo_ciudad, #grupo_nombre_gerente').val(null).prop('disabled', true);
            $('#grupo_region, #grupo_ciudad, #grupo_nombre_gerente').val('').prop('disabled', true);
        } else {
            // Limpia los valores
            $('#recinto').empty();
            // Agrega sólo los recintos 
            $('#recinto').append(`
                <option value="" selected disabled>Selecciona un recinto</option>
                <option value='Acambaro'>Acambaro</option>
                <option value='Acaponeta'>Acaponeta</option>
                <option value='Acatic'>Acatic</option>
                <option value='Acayucan'>Acayucan</option>
                <option value='Actopan'>Actopan</option>
                <option value='Agua Dulce'>Agua Dulce</option>
                <option value='Ahuacatlan'>Ahuacatlan</option>
                <option value='Ahualulco'>Ahualulco</option>
                <option value='Ahuateno'>Ahuateno</option>
                <option value='Aldrete'>Aldrete</option>
                <option value='Allende'>Allende</option>
                <option value='Altamira Centro'>Altamira Centro</option>
                <option value='Altea Huinala'>Altea Huinala</option>
                <option value='Altea Lincoln'>Altea Lincoln</option>
                <option value='Altea Rio Nilo'>Altea Rio Nilo</option>
                <option value='Alvarado'>Alvarado</option>
                <option value='Amapolas'>Amapolas</option>
                <option value='Amatitan'>Amatitan</option>
                <option value='Amatlan'>Amatlan</option>
                <option value='Amaxac'>Amaxac</option>
                <option value='Amealco'>Amealco</option>
                <option value='Ameca'>Ameca</option>
                <option value='Amecameca'>Amecameca</option>
                <option value='Antenas'>Antenas</option>
                <option value='Apan'>Apan</option>
                <option value='Apodaca'>Apodaca</option>
                <option value='Arandas Centro'>Arandas Centro</option>
                <option value='Arboledas Altamira'>Arboledas Altamira</option>
                <option value='Arenal'>Arenal</option>
                <option value='Arenas'>Arenas</option>
                <option value='Armeria'>Armeria</option>
                <option value='Atemajac'>Atemajac</option>
                <option value='Atizapan'>Atizapan</option>
                <option value='Atlixco'>Atlixco</option>
                <option value='Azcapotzalco'>Azcapotzalco</option>
                <option value='Barragan'>Barragan</option>
                <option value='Benton'>Benton</option>
                <option value='Bosques de Aragon'>Bosques de Aragon</option>
                <option value='Bucerias'>Bucerias</option>
                <option value='Bugambilias'>Bugambilias</option>
                <option value='Cacahoatan'>Cacahoatan</option>
                <option value='Cadereyta'>Cadereyta</option>
                <option value='Calera'>Calera</option>
                <option value='Calvillo'>Calvillo</option>
                <option value='Camargo'>Camargo</option>
                <option value='Cancun Mall'>Cancun Mall</option>
                <option value='Capilla de Guadalupe'>Capilla de Guadalupe</option>
                <option value='Cardenas'>Cardenas</option>
                <option value='Carranza'>Carranza</option>
                <option value='Casimiro Castillo'>Casimiro Castillo</option>
                <option value='Castillo de Teayo'>Castillo de Teayo</option>
                <option value='Catemaco'>Catemaco</option>
                <option value='CD Mendoza'>CD Mendoza</option>
                <option value='Cd.Sahagun'>Cd.Sahagun</option>
                <option value='Cedral'>Cedral</option>
                <option value='Centro Sur GDL'>Centro Sur GDL</option>
                <option value='Chalco'>Chalco</option>
                <option value='Chapala Centro'>Chapala Centro</option>
                <option value='Chapultepec'>Chapultepec</option>
                <option value='Chetumal'>Chetumal</option>
                <option value='Chichen Itza'>Chichen Itza</option>
                <option value='Chietla'>Chietla</option>
                <option value='Chimalhuacan'>Chimalhuacan</option>
                <option value='Chocaman'>Chocaman</option>
                <option value='Cholula'>Cholula</option>
                <option value='Cigarrera'>Cigarrera</option>
                <option value='Cihuatlan'>Cihuatlan</option>
                <option value='Citadina'>Citadina</option>
                <option value='CM Celaya'>CM Celaya</option>
                <option value='Coapa'>Coapa</option>
                <option value='Coatzacoalcos Centro'>Coatzacoalcos Centro</option>
                <option value='Coatzacoalcos Quadrum'>Coatzacoalcos Quadrum</option>
                <option value='Coatzintla'>Coatzintla</option>
                <option value='Comalcalco'>Comalcalco</option>
                <option value='Compostela'>Compostela</option>
                <option value='Cordilleras'>Cordilleras</option>
                <option value='Coscomatepec'>Coscomatepec</option>
                <option value='Cosoleacaque'>Cosoleacaque</option>
                <option value='Cozumel'>Cozumel</option>
                <option value='Crystal Cordoba'>Crystal Cordoba</option>
                <option value='Crystal Tuxpan'>Crystal Tuxpan</option>
                <option value='Cuajimalpa'>Cuajimalpa</option>
                <option value='Cuauhtemoc'>Cuauhtemoc</option>
                <option value='Cuautepec'>Cuautepec</option>
                <option value='Cuautitlan Izcalli'>Cuautitlan Izcalli</option>
                <option value='Cuernavaca Centro'>Cuernavaca Centro</option>
                <option value='Cuitlahuac'>Cuitlahuac</option>
                <option value='Cunduacan'>Cunduacan</option>
                <option value='Delicias'>Delicias</option>
                <option value='El Dorado'>El Dorado</option>
                <option value='El Parian'>El Parian</option>
                <option value='El Salto'>El Salto</option>
                <option value='Ermita'>Ermita</option>
                <option value='Escobedo'>Escobedo</option>
                <option value='Etzatlan'>Etzatlan</option>
                <option value='Fashion Mall'>Fashion Mall</option>
                <option value='Federico Medrano'>Federico Medrano</option>
                <option value='Fortin'>Fortin</option>
                <option value='Forum Tlaquepaque'>Forum Tlaquepaque</option>
                <option value='Fresnillo Centro'>Fresnillo Centro</option>
                <option value='Fuentes Mares'>Fuentes Mares</option>
                <option value='Galerias Campeche'>Galerias Campeche</option>
                <option value='Garcia'>Garcia</option>
                <option value='Garcia Salinas'>Garcia Salinas</option>
                <option value='Gastelum'>Gastelum</option>
                <option value='Gomez Farias'>Gomez Farias</option>
                <option value='Gran Plaza Cancun'>Gran Plaza Cancun</option>
                <option value='Gran Plaza Mazatlan'>Gran Plaza Mazatlan</option>
                <option value='Gran Plaza Merida'>Gran Plaza Merida</option>
                <option value='Gran Sur Pachuca'>Gran Sur Pachuca</option>
                <option value='Guerrero'>Guerrero</option>
                <option value='Guerrero Pachuca'>Guerrero Pachuca</option>
                <option value='Haciendas'>Haciendas</option>
                <option value='HB Coyoacan'>HB Coyoacan</option>
                <option value='HB Nogales'>HB Nogales</option>
                <option value='HB Pachuca'>HB Pachuca</option>
                <option value='HB Panuco'>HB Panuco</option>
                <option value='HB Plaza Chalco'>HB Plaza Chalco</option>
                <option value='HB Plaza Cumbres'>HB Plaza Cumbres</option>
                <option value='HB Tehuacan'>HB Tehuacan</option>
                <option value='HB Via Salamanca'>HB Via Salamanca</option>
                <option value='Heroes Tecamac'>Heroes Tecamac</option>
                <option value='Hidalgo Reynosa'>Hidalgo Reynosa</option>
                <option value='Huamantla'>Huamantla</option>
                <option value='Huatusco'>Huatusco</option>
                <option value='Iguala'>Iguala</option>
                <option value='Iramuco'>Iramuco</option>
                <option value='Isla Mujeres'>Isla Mujeres</option>
                <option value='Ixtlan'>Ixtlan</option>
                <option value='Izcalli del Valle'>Izcalli del Valle</option>
                <option value='Iztapalapa'>Iztapalapa</option>
                <option value='Izucar de Matamoros'>Izucar de Matamoros</option>
                <option value='Jala'>Jala</option>
                <option value='Jalostotitlan'>Jalostotitlan</option>
                <option value='Jalpa'>Jalpa</option>
                <option value='Jaltipan'>Jaltipan</option>
                <option value='Jamay'>Jamay</option>
                <option value='Jesus Ma.Tepatitlan'>Jesus Ma.Tepatitlan</option>
                <option value='Jiutepec'>Jiutepec</option>
                <option value='Jocotepec'>Jocotepec</option>
                <option value='Juarez'>Juarez</option>
                <option value='La Barca Guerrero'>La Barca Guerrero</option>
                <option value='La Cima'>La Cima</option>
                <option value='La Encantada'>La Encantada</option>
                <option value='La Fama'>La Fama</option>
                <option value='Lagos Capuchinas'>Lagos Capuchinas</option>
                <option value='Lardizabal'>Lardizabal</option>
                <option value='Las Aguilas'>Las Aguilas</option>
                <option value='Las Americas QRO'>Las Americas QRO</option>
                <option value='Las Americas Tabasco'>Las Americas Tabasco</option>
                <option value='Las Choapas'>Las Choapas</option>
                <option value='Las Torres'>Las Torres</option>
                <option value='Las Torres GDL'>Las Torres GDL</option>
                <option value='Lauro Villar'>Lauro Villar</option>
                <option value='Lerdo De Tejada'>Lerdo De Tejada</option>
                <option value='Libertad'>Libertad</option>
                <option value='Linares'>Linares</option>
                <option value='Lincoln'>Lincoln</option>
                <option value='Linda Vista'>Linda Vista</option>
                <option value='Lopez Velarde'>Lopez Velarde</option>
                <option value='Loreto'>Loreto</option>
                <option value='Los Heroes QRO'>Los Heroes QRO</option>
                <option value='Los Reyes Acozac'>Los Reyes Acozac</option>
                <option value='Los Reyes Higuerita'>Los Reyes Higuerita</option>
                <option value='Los Reyes la Paz'>Los Reyes la Paz</option>
                <option value='Macultepec'>Macultepec</option>
                <option value='Macuspana'>Macuspana</option>
                <option value='Madero'>Madero</option>
                <option value='Madero Durango'>Madero Durango</option>
                <option value='Madero Rio Bravo'>Madero Rio Bravo</option>
                <option value='Malda'>Malda</option>
                <option value='Maneadero'>Maneadero</option>
                <option value='Mante'>Mante</option>
                <option value='Manuel Doblado'>Manuel Doblado</option>
                <option value='Manzanillo Centro'>Manzanillo Centro</option>
                <option value='Matriz Cd Del Carmen'>Matriz Cd Del Carmen</option>
                <option value='Matriz Celaya'>Matriz Celaya</option>
                <option value='Matriz Cordoba'>Matriz Cordoba</option>
                <option value='Matriz Matehuala'>Matriz Matehuala</option>
                <option value='Matriz Mexicali'>Matriz Mexicali</option>
                <option value='Matriz Monclova'>Matriz Monclova</option>
                <option value='Matriz Morelos'>Matriz Morelos</option>
                <option value='Matriz Orizaba'>Matriz Orizaba</option>
                <option value='Matriz Saltillo'>Matriz Saltillo</option>
                <option value='Matriz San Luis'>Matriz San Luis</option>
                <option value='Matriz Vallarta'>Matriz Vallarta</option>
                <option value='Matriz Victoria'>Matriz Victoria</option>
                <option value='Matriz Zaragoza'>Matriz Zaragoza</option>
                <option value='Melchor Ocampo'>Melchor Ocampo</option>
                <option value='Meoqui'>Meoqui</option>
                <option value='Minatitlan'>Minatitlan</option>
                <option value='Miravalle'>Miravalle</option>
                <option value='Modulo Centro Merida'>Modulo Centro Merida</option>
                <option value='Montejo'>Montejo</option>
                <option value='Montemorelos'>Montemorelos</option>
                <option value='Morelos'>Morelos</option>
                <option value='Motul'>Motul</option>
                <option value='Nacajuca'>Nacajuca</option>
                <option value='Naciones'>Naciones</option>
                <option value='Nanchital'>Nanchital</option>
                <option value='Nochistlan'>Nochistlan</option>
                <option value='Nogalera'>Nogalera</option>
                <option value='Nuevo Sur'>Nuevo Sur</option>
                <option value='Ocampo'>Ocampo</option>
                <option value='Ocotlan'>Ocotlan</option>
                <option value='Oficina Central Acapulco'>Oficina Central Acapulco</option>
                <option value='Oficina Central Chilpancingo'>Oficina Central Chilpancingo</option>
                <option value='Oficina Central Cuautla'>Oficina Central Cuautla</option>
                <option value='Ojo De Agua'>Ojo De Agua</option>
                <option value='Ojuelos'>Ojuelos</option>
                <option value='Pabellon Arteaga'>Pabellon Arteaga</option>
                <option value='Pabellon Cuauhtemoc'>Pabellon Cuauhtemoc</option>
                <option value='Pabellon del Valle'>Pabellon del Valle</option>
                <option value='Pabellon Rosarito'>Pabellon Rosarito</option>
                <option value='Palmas'>Palmas</option>
                <option value='Papantla'>Papantla</option>
                <option value='Paraiso'>Paraiso</option>
                <option value='Parque Celaya'>Parque Celaya</option>
                <option value='Parque Lindavista'>Parque Lindavista</option>
                <option value='Parral'>Parral</option>
                <option value='Parrilla'>Parrilla</option>
                <option value='Paseo Acoxpa'>Paseo Acoxpa</option>
                <option value='Paseo San Luis'>Paseo San Luis</option>
                <option value='Paseo Tec'>Paseo Tec</option>
                <option value='Patio Chalco'>Patio Chalco</option>
                <option value='Patio Santa Fe'>Patio Santa Fe</option>
                <option value='Patio Texcoco'>Patio Texcoco</option>
                <option value='Patio Toluca'>Patio Toluca</option>
                <option value='Patio Tulancingo'>Patio Tulancingo</option>
                <option value='Patriotismo'>Patriotismo</option>
                <option value='Periban'>Periban</option>
                <option value='Pie De La Cuesta'>Pie De La Cuesta</option>
                <option value='Piedras Negras'>Piedras Negras</option>
                <option value='Pipila'>Pipila</option>
                <option value='Plan de Ayala'>Plan de Ayala</option>
                <option value='Playa del Carmen'>Playa del Carmen</option>
                <option value='Playas'>Playas</option>
                <option value='Playas Del Rosario'>Playas Del Rosario</option>
                <option value='Plaza Aragon'>Plaza Aragon</option>
                <option value='Plaza Aviacion'>Plaza Aviacion</option>
                <option value='Plaza Bella'>Plaza Bella</option>
                <option value='Plaza Bella Pachuca'>Plaza Bella Pachuca</option>
                <option value='Plaza Citadina QRO'>Plaza Citadina QRO</option>
                <option value='Plaza Cortijo'>Plaza Cortijo</option>
                <option value='Plaza Crystal'>Plaza Crystal</option>
                <option value='Plaza Deportiva'>Plaza Deportiva</option>
                <option value='Plaza Ecatepec'>Plaza Ecatepec</option>
                <option value='Plaza Espinal'>Plaza Espinal</option>
                <option value='Plaza Fiesta'>Plaza Fiesta</option>
                <option value='Plaza Fiesta Mtm'>Plaza Fiesta Mtm</option>
                <option value='Plaza Hisa'>Plaza Hisa</option>
                <option value='Plaza Jacarandas'>Plaza Jacarandas</option>
                <option value='Plaza Jardin'>Plaza Jardin</option>
                <option value='Plaza Jilotepec'>Plaza Jilotepec</option>
                <option value='Plaza las Flores'>Plaza las Flores</option>
                <option value='Plaza Monumental'>Plaza Monumental</option>
                <option value='Plaza Periferico'>Plaza Periferico</option>
                <option value='Plaza Poncitlan'>Plaza Poncitlan</option>
                <option value='Plaza Real'>Plaza Real</option>
                <option value='Plaza Santa Julia'>Plaza Santa Julia</option>
                <option value='Plaza Sendero'>Plaza Sendero</option>
                <option value='Plaza Uno'>Plaza Uno</option>
                <option value='Polanco'>Polanco</option>
                <option value='Portal Durango'>Portal Durango</option>
                <option value='Portales'>Portales</option>
                <option value='Poza Rica'>Poza Rica</option>
                <option value='Progreso'>Progreso</option>
                <option value='Purepero'>Purepero</option>
                <option value='Ramos Arizpe'>Ramos Arizpe</option>
                <option value='Reforma Laredo'>Reforma Laredo</option>
                <option value='Republica'>Republica</option>
                <option value='Revolucion'>Revolucion</option>
                <option value='Reynosa Citadina'>Reynosa Citadina</option>
                <option value='Riberas'>Riberas</option>
                <option value='Rincon De Romos'>Rincon De Romos</option>
                <option value='Rio'>Rio</option>
                <option value='Rio Blanco'>Rio Blanco</option>
                <option value='Rio Colorado'>Rio Colorado</option>
                <option value='Sabinas'>Sabinas</option>
                <option value='Salvatierra'>Salvatierra</option>
                <option value='San Andres Tuxtla'>San Andres Tuxtla</option>
                <option value='San Buenaventura'>San Buenaventura</option>
                <option value='San Francisco de los Romos'>San Francisco de los Romos</option>
                <option value='San Gaspar'>San Gaspar</option>
                <option value='San Ignacio'>San Ignacio</option>
                <option value='San Jose'>San Jose</option>
                <option value='San Juan de los lagos'>San Juan de los lagos</option>
                <option value='San Juan Del Rio'>San Juan Del Rio</option>
                <option value='San Julian'>San Julian</option>
                <option value='San Lorenzo'>San Lorenzo</option>
                <option value='San Luis Centro'>San Luis Centro</option>
                <option value='San Miguel'>San Miguel</option>
                <option value='San Nicolas'>San Nicolas</option>
                <option value='San Roque'>San Roque</option>
                <option value='Santa Ana'>Santa Ana</option>
                <option value='Santa Ana Maya'>Santa Ana Maya</option>
                <option value='Santa Ana Pacueco'>Santa Ana Pacueco</option>
                <option value='Santa Maria'>Santa Maria</option>
                <option value='Santa Rosa'>Santa Rosa</option>
                <option value='Santiago'>Santiago</option>
                <option value='Santiago Tuxtla'>Santiago Tuxtla</option>
                <option value='Santo Domingo'>Santo Domingo</option>
                <option value='Satelite'>Satelite</option>
                <option value='Satelite QRO'>Satelite QRO</option>
                <option value='Sendero San Luis'>Sendero San Luis</option>
                <option value='SH Tlaxcala'>SH Tlaxcala</option>
                <option value='Soriana Manzanillo'>Soriana Manzanillo</option>
                <option value='Tala'>Tala</option>
                <option value='Tamatan'>Tamatan</option>
                <option value='Tampico Aeropuerto'>Tampico Aeropuerto</option>
                <option value='Tampico Centro'>Tampico Centro</option>
                <option value='Tapachula Centro'>Tapachula Centro</option>
                <option value='Taxco'>Taxco</option>
                <option value='Tecuala'>Tecuala</option>
                <option value='Temozon'>Temozon</option>
                <option value='Teocaltiche'>Teocaltiche</option>
                <option value='Teotihuacan'>Teotihuacan</option>
                <option value='Tepatitlan Los Altos'>Tepatitlan Los Altos</option>
                <option value='Tepeji'>Tepeji</option>
                <option value='Tepexpan'>Tepexpan</option>
                <option value='Tepeyac'>Tepeyac</option>
                <option value='Tepotzotlan'>Tepotzotlan</option>
                <option value='Tequila'>Tequila</option>
                <option value='Tesistan'>Tesistan</option>
                <option value='Teziutlan Centro'>Teziutlan Centro</option>
                <option value='Tezontle'>Tezontle</option>
                <option value='Tihuatlan'>Tihuatlan</option>
                <option value='Tizayuca'>Tizayuca</option>
                <option value='Tizimin'>Tizimin</option>
                <option value='Tlajomulco Centro'>Tlajomulco Centro</option>
                <option value='Tlaltelulco'>Tlaltelulco</option>
                <option value='Tlatelolco'>Tlatelolco</option>
                <option value='Toluca Centro'>Toluca Centro</option>
                <option value='Torres Lindavista'>Torres Lindavista</option>
                <option value='Town Center'>Town Center</option>
                <option value='Tula'>Tula</option>
                <option value='Tulancingo Centro'>Tulancingo Centro</option>
                <option value='Tulum'>Tulum</option>
                <option value='Tuxpan'>Tuxpan</option>
                <option value='Tuxtla Chico'>Tuxtla Chico</option>
                <option value='Up Town Juriquilla'>Up Town Juriquilla</option>
                <option value='Urbi'>Urbi</option>
                <option value='Valladolid'>Valladolid</option>
                <option value='Vallarta'>Vallarta</option>
                <option value='Valle De Los Molinos'>Valle De Los Molinos</option>
                <option value='Valle De Santiago'>Valle De Santiago</option>
                <option value='Valle Hermoso'>Valle Hermoso</option>
                <option value='Valles'>Valles</option>
                <option value='Versalles'>Versalles</option>
                <option value='Villa Fontana'>Villa Fontana</option>
                <option value='Villa Hidalgo'>Villa Hidalgo</option>
                <option value='Villas del Cielo'>Villas del Cielo</option>
                <option value='Villasuncion'>Villasuncion</option>
                <option value='Walmart la Estancia'>Walmart la Estancia</option>
                <option value='Yahualica'>Yahualica</option>
                <option value='Yautepec'>Yautepec</option>
                <option value='Yuriria'>Yuriria</option>
                <option value='Zacapu'>Zacapu</option>
                <option value='Zapata'>Zapata</option>
                <option value='Zapotiltic'>Zapotiltic</option>
                <option value='Zapotlanejo'>Zapotlanejo</option>
                <option value='Zumpango'>Zumpango</option>
                <option value='Zumpango del Rio'>Zumpango del Rio</option>

            `);

            // Muestra los campos (actividad != 'Otras Actividades')
            $('#grupo_region, #grupo_ciudad, #grupo_nombre_gerente').removeClass('d-none');
            // Activa los campos nuevamente
            $('#grupo_region, #grupo_ciudad, #grupo_nombre_gerente').prop('disabled', false);
        }

    })


    $("#recinto").change(function () {
        var recinto = $(this).val();

        $.get("_buscaGerente.php", { recinto: recinto })
            .done(function (data) {
                $("#resultado_consulta").html(data);
                $("#text_region").val($("#region").val());
                $("#text_ciudad").val($("#ciudad").val());
                $("#text_nombre_gerente").val($("#nombre_gerente").val());
            });
    });

    $("#form_registro_actividad_cdm").validate({
        ignore: ':hidden:not(select)',
        onfocusout: false,
        rules: {
            actividad: { required: true },
            incidencia: {
                required: function () {
                    return $('#actividad').val() !== 'Otras Actividades';
                }
            },
            tipo: {
                required: function () {
                    return $('#actividad').val() !== 'Otras Actividades';
                }
            },
            recinto: {
                required: function () {
                    return $('#actividad').val() !== 'Otras Actividades';
                }
            },
            text_region: {
                required: function () {
                    return $('#actividad').val() !== 'Otras Actividades' && $('#tipo').val() !== 'Call Center';
                }
            },
            text_ciudad: {
                required: function () {
                    return $('#actividad').val() !== 'Otras Actividades' && $('#tipo').val() !== 'Call Center';
                }
            },
            text_nombre_gerente: {
                required: function () {
                    return $('#actividad').val() !== 'Otras Actividades' && $('#tipo').val() !== 'Call Center';
                }
            },
            comentarios: {
                required: function () {
                    return $('#actividad').val() === 'Otras Actividades';
                },
                maxlength: 1000
            }
        },
        submitHandler: function (form) {
            // Renovar sesión antes de guardar
            fetch('_renovar_sesion_copia.php', {
                method: 'GET',
                credentials: 'include'
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'OK') {
                        console.log(`Se actualiza la sesión desde el botón guardar del formulario, nueva sesión: ${data.nuevo_ultimo_acceso}`);

                        // Continuar con el envío original del formulario
                        var formElement = $('#form_registro_actividad_cdm')[0];
                        var data = new FormData(formElement);

                        let hora_inicio_raw = $('#hora_inicio').val();
                        let hora_inicio_ = hora_inicio_raw.length === 5 ? hora_inicio_raw + ':00' : hora_inicio_raw;
                        let fechaInicio = new Date('1970-01-01T' + hora_inicio_);

                        let now = new Date();
                        let hora_fin = [
                            String(now.getHours()).padStart(2, '0'),
                            String(now.getMinutes()).padStart(2, '0'),
                            String(now.getSeconds()).padStart(2, '0')
                        ].join(':');

                        let hora_fin_ = hora_fin;
                        let fechaFin = new Date('1970-01-01T' + hora_fin_);

                        if (fechaFin < fechaInicio) {
                            fechaFin.setDate(fechaFin.getDate() + 1);
                        }

                        let diferencia = fechaFin - fechaInicio;
                        let tiempoFormateado = convertirMilisegundosAHorasMinutosSegundos(diferencia);
                        let tiempoLegible = convertirMilisegundosALegible(diferencia);

                        $('#hora_fin').val(hora_fin);
                        $('#text_tiempo').val(tiempoFormateado);
                        $('#text_tiempoDeshabilitado').val(tiempoFormateado);
                        $('#grupo_hora_fin').removeClass('d-none');
                        $('#grupo_tiempo').removeClass('d-none');

                        text_tiempo = tiempoFormateado;

                        let actividad = $('#actividad').val();
                        let comentarios = $('#comentarios').val();

                        data.append('hora_fin', hora_fin);
                        data.append('text_tiempo', text_tiempo);
                        data.append('actividad', actividad);
                        data.append('comentarios', comentarios);

                        $.ajax({
                            url: 'guarda.php',
                            type: 'POST',
                            enctype: 'multipart/form-data',
                            processData: false,
                            contentType: false,
                            cache: false,
                            dataType: 'html',
                            data: data,
                            beforeSend: function () {
                                $("#form_button").val("Guardando...");
                                $("#form_button").prop("disabled", true);
                            },
                            complete: function () {
                                console.info("Comunicación ok");
                            },
                            success: function (data) {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Registro exitoso',
                                    html: `<p>${data}</p><p>Tiempo capturado: <strong>${tiempoLegible}</strong></p>`,
                                    showConfirmButton: false,
                                    timer: 10000,
                                    timerProgressBar: true
                                });

                                setTimeout(function () {
                                    window.location.href = 'home.php';
                                }, 10000);
                            },
                            error: function () {
                                console.info("Hay un error al procesar el formulario.");
                            }
                        });

                    } else {
                        // Sesión vencida
                        Swal.fire('Sesión expirada', 'Por favor inicia sesión nuevamente.', 'error')
                            .then(() => window.location.href = 'index.php');
                    }
                })
                .catch(error => {
                    console.error('Error al renovar la sesión antes de guardar:', error);
                    Swal.fire('Error', 'No se pudo renovar la sesión. Intenta nuevamente.', 'error');
                });
        }
    });
});
