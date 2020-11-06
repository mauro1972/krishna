//import IntMannager  from './interpretacionesMannager.js';
import IntMannager  from './interpretacionController.js';
const intMannager = new IntMannager();

$ = jQuery;

class charts_controller {
    
    constructor() {
        
        this.DOMstrings = {
            createChartFormId: 'chart-form',
            inputCreateChart: '.chart-form__submit',  
            inputName: '.chart-form__name',
            inputSecondName: '.chart-form__second_name',
            inputFatherLastame: '.chart-form__father_lastname',
            inputMotherLastame: '.chart-form__mother_lastname',
            inputBirthDate: '.chart-form__birth_date',
            chartNumbers: '.chart-numbers',
            nameNumber: '.chart-numbers__name',
            buttonCreateChart: '.button-create-chart',
            dataWrapper: 'chart-data-wrapper',
            container: '.int-list',
            buttonSaveContent: '.button-save-chart',

            buttonCreatePronostico: '.pronostico-form__submit',
        }
      
        this.events();

    }
    
    events() {
        var DOM = this.get_DOMstrings();
        document.querySelector(DOM.inputCreateChart).addEventListener('click', this.createChart.bind(this));
        
        document.getElementById('chart-data-wrapper').addEventListener('click', this.changeLetterType.bind(this));
        
        document.querySelector(DOM.buttonCreateChart).addEventListener('click', this.calculateNumbers.bind(this));
        
        // Selecciona la interpretacion para la carta
        var intList =   document.querySelector(DOM.container);
        if (intList){
            document.querySelector(DOM.container).addEventListener('click', this.saveSectionContent.bind(this));
        }
        
        // vuelca las interpretaciones elegidas al contenido del post type carta.
         document.querySelector(DOM.buttonSaveContent).addEventListener('click', this.saveContentToChart.bind(this));

         // create pronostico.
         document.querySelector( DOM.buttonCreatePronostico ).addEventListener( 'click', this.createPronostico.bind( this ) );
    }
    
    get_DOMstrings() {
        return this.DOMstrings;
    }
    
    // Methods
    createPronostico( e ) {
        e.preventDefault();
        var DOM = this.get_DOMstrings();
        var carta = document.getElementById("choice-chart");
        var chartID = carta.options[carta.selectedIndex].value;
        var year = document.getElementById( 'pronostico-year' ).value;
        console.log( chartID +' '+ year );
        // data for ajax
        var datos = {
            'action': 'create-pronostico',
            'chartID': chartID,
            'year': year,
        }
        
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/chart',
            type: 'GET',
            data: datos,
            success: (response) => {
                
                console.log(response);
                window.location = response.path;
                
            },
            error: (response) => {
                console.log(response);
            },
        });        
        
    }

    saveContentToChart(e) {
        var DOM = this.get_DOMstrings();

        var post_id = document.querySelector(DOM.buttonSaveContent).getAttribute('data-id');

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/save-chart-content',
            type: 'GET',
            data: {
              'post_id': post_id,
                
            },
            success: (response) => {
                console.log(response);
                location.reload();
            },
            error: (response) => {
                console.log(response);
            },           
        });
    }
    
    saveSectionContent(e) {
        
        var contentData;
        
        contentData = {
            postID: e.target.parentNode.parentNode.parentNode.id,
            intID: e.target.getAttribute('data-id'),
            section: e.target.getAttribute('data-section')          
        }
        

        $.ajax({
              beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/chart-content',
            type: 'GET',
            data: {
              'contentData': contentData,
                
            },
            success: (response) => {
                console.log(response);
                location.reload();
            },
            error: (response) => {
                console.log(response);
            },           
        });
    }
    
    calculateNumbers(e) {
        var DOM = this.get_DOMstrings();
        var postID;
        
        postID = document.getElementById(DOM.dataWrapper).getAttribute('data-id');
        
        $.ajax({
             beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/calculate-numbers',
            type: 'GET',
            data: {
              'postID': postID, 
            },
            success: (response) => {
                console.log(response);
                location.reload();
            },
            error: (response) => {
                console.log(response);
            },           
        });

    }
    
    changeLetterType(e) {
        
        var letterIndex, letterSection, chartData, postId;
        
        chartData = {
            letterIndex: e.target.parentNode.getAttribute('id'),
            letterSection: e.target.parentNode.parentNode.getAttribute('id'),
            postId: e.target.parentNode.parentNode.parentNode.getAttribute('data-id'),
        }

        
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/chart-data',
            type: 'GET',
            data: {
              'chartData': chartData, 
            },
            success: (response) => {
                if (response === 'cons') {
                    console.log('chage to vocal');
                    e.target.parentNode.firstChild.textContent = 'vocal';
                } else {
                   console.log('chage to cons'); 
                   e.target.parentNode.firstChild.textContent = 'cons';
                }
            },
            error: (response) => {
                console.log(response);
            },
        });
        
        
        
    }
    
    createChart(event) {
        event.preventDefault();

        var DOM = this.get_DOMstrings();
        var name, secondName, fatherLastname, motherLastname, birthDate;
        
        var formData = {};
        
        formData = {
            name: document.querySelector(DOM.inputName).value,
            secondName: document.querySelector(DOM.inputSecondName).value,
            fatherLastname: document.querySelector(DOM.inputFatherLastame).value,
            motherLastname: document.querySelector(DOM.inputMotherLastame).value,
            birthDate: document.querySelector(DOM.inputBirthDate).value
        }
        
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/chart',
            type: 'GET',
            data: {
                'formData': formData,
            }, 
            success: (response) => {
                
                console.log(response);
                window.location = response.permalink;
                
            },
            error: (response) => {
                console.log(response);
            },
        });
    }
}

var charts = new charts_controller();