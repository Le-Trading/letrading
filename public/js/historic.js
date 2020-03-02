$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip()

    //traduction mois
    var monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
        "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
    ];

    //recup date actuelle
    const now = new Date();
    $('#selectedMonth1').append(monthNames[now.getMonth()] + " " + now.getFullYear());
    $('#selectedMonth2').append(monthNames[now.getMonth()] + " " + now.getFullYear());

    //traduction datepicker francais
    $.fn.datepicker.dates['fr'] = {
        days: ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
        daysShort: ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
        daysMin: ["d", "l", "ma", "me", "j", "v", "s"],
        months: ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
        monthsShort: ["janv.", "févr.", "mars", "avril", "mai", "juin", "juil.", "août", "sept.", "oct.", "nov.", "déc."]
    };

    //initialisation du datepicker pour choisir un mois
    $('#selectedDate1').datepicker({
        language: 'fr',
        format: "mm-yyyy",
        viewMode: "months",
        minViewMode: "months",
        orientation: "auto"
    });
    $('#selectedDate1').datepicker('setDate', 'today');

    $('#selectedDate2').datepicker({
        language: 'fr',
        format: "mm-yyyy",
        viewMode: "months",
        minViewMode: "months",
        orientation: "auto"
    });
    $('#selectedDate2').datepicker('setDate', 'today');

    //recuperation date datepicker + date des résultats
    $.fn.dataTable.ext.search.push(
        function (settings, data) {
            //date du datepicker
            var dateFromFilter = null;
            if(settings.sTableId === "tableHistoric1"){
                dateFromFilter = $('#selectedDate1').datepicker("getDate");
            }else if(settings.sTableId === "tableHistoric2"){
                dateFromFilter = $('#selectedDate2').datepicker("getDate");
            }
            if(dateFromFilter){
                var monthFromFilter = dateFromFilter.getMonth() + 1;
                var yearFromFilter = dateFromFilter.getFullYear();
            }

            //date du tableau
            var postDate = new Date(data[0]);
            var monthPost = postDate.getMonth()+1;
            var yearPost = postDate.getFullYear();

            //on affiche les posts en fonction de la date choisie
            if((monthFromFilter === monthPost) && (yearFromFilter === yearPost)) {return true;}
            return false;
        }
    );

    //creation tableau historique
    var table1 = $('#tableHistoric1').DataTable({
        "info": false,
        "responsive": true,
        "order": [[ 0, "desc" ]],
        "bLengthChange" : false,
        "paging": false,
        "language": {
            "zeroRecords":    "Aucun résultat pour le mois séléctionné"
        }
    });
    $( table1.table().container() ).removeClass( 'form-inline' );

    var table2 = $('#tableHistoric2').DataTable({
        "info": false,
        "responsive": true,
        "order": [[ 0, "desc" ]],
        "bLengthChange" : false,
        "paging": false,
        "language": {
            "zeroRecords":    "Aucun résultat pour le mois séléctionné"
        }
    });
    $( table2.table().container() ).removeClass( 'form-inline' );

    //appliquer changement de date aux résultats
    $('#selectedDate1').change(function () {
        //récuperation mois et année séléctionné
        if($('#selectedDate1').datepicker("getDate")) {
            var monthSelected = monthNames[$('#selectedDate1').datepicker("getDate").getMonth()];
            var yearSelected = $('#selectedDate1').datepicker("getDate").getFullYear();
            $('#selectedMonth1').html(monthSelected + " " + yearSelected);

            table1.draw();
        }
    });
    $('#selectedDate2').change(function () {
        //récuperation mois et année séléctionné
        if($('#selectedDate2').datepicker("getDate")){
            var monthSelected = monthNames[$('#selectedDate2').datepicker("getDate").getMonth()];
            var yearSelected = $('#selectedDate2').datepicker("getDate").getFullYear();
            $('#selectedMonth2').html(monthSelected + " " + yearSelected);

            table2.draw();
        }
    });
});