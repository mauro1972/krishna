export default class InterpretacionController {

    constructor() {
        this.modalStatus = false;
        this.tipo = '';
        this.number = '';
        this.order = '';
        this.proId = '';
        this.action = '';
        this.init();
        this.events();
    }

    init() {
        if ( document.querySelector( '.type-pronostico' ) ) {
            this.proId = document.querySelector( '.type-pronostico' ).getAttribute( 'id' );
        }
    }

    events() {
        document.body.addEventListener( 'click', this.btnClicked.bind( this ) );

        if ( document.querySelector( '.btn--save-pro' ) ) {
            document.querySelector( '.btn--save-pro' ).addEventListener( 'click', this.saveProContent.bind( this ) );
        }

        if ( document.querySelector( '.btn--reset-pro' ) ) {
            document.querySelector( '.btn--reset-pro' ).addEventListener( 'click', this.resetProContent.bind( this ) );
        }        

        this.closeModal();

    }

    closeModal() {
        if ( document.querySelector( '.editor-box--close') ) {
            document.querySelector( '.editor-box--close').addEventListener( 'click', this.modalMannager.bind( this ) );
        }
    }

    resetProContent(e) {
        e.preventDefault();

        let api = 'save-chart-content';
        this.action = 'reset-pro-content';

        let datos = {
            'proId': this.proId,
            'action': this.action,
        }  

        this.saveProAjaxCall( datos, api );
    }

    /**
     * Saves the selected interpretations to the pronostico content.
     * 
     * @param {*} e 
     */
    saveProContent(e) {
        e.preventDefault();

        let api = 'save-chart-content';
        this.action = 'save-pro-content';

        let datos = {
            'proId': this.proId,
            'action': this.action,
        }
        
        this.saveProAjaxCall( datos, api );
    }

    /**
     * Filter button clicked.
     * 
     * @param {*} e target element.
     */
    btnClicked(e) {

        console.log( e.target );
        // TODO: use the same function that controls create inter modal box.
        // thing is; select modal is created after DOM is render.
        if ( e.target.classList.contains( 'btn--close' ) ) {
            document.querySelector( '.num-modal-box' ).remove();
        }
       
        // Add New Interpretacion button.
        if ( e.target.classList.contains( 'add-inter' ) ) {
            e.preventDefault();
            
            this.loadProParameters(e); // Set values into interpretayion parameters global variables.

            this.modalMannager(); // Show Modal Box.

        }

        if ( e.target.classList.contains( 'save-content' ) ) {

            e.preventDefault();

            // Set data to send with ajax.
            let api = 'save-chart-content'; // we use the same api for cartas content type.
            let title = document.querySelector("[name='inter-title']").value;
            let content = tinymce.activeEditor.getContent();
            this.action = 'create-inter';
            let datos = {
                'tipo': this.tipo,
                'number': this.number,
                'content':  content,
                'title': title,
                'pro_id': this.proId,
                'order': this.order,
                'action': this.action
            };            

            this.saveProAjaxCall( datos, api );
        }        

        // Select Among avaliable interpretations.
        if ( e.target.classList.contains( 'btn--select-inter' ) ) {
            e.preventDefault();
            
            this.loadProParameters(e);

            let api = 'save-chart-content';
            this.action = 'load-options';
            let datos = {
                'tipo': this.tipo,
                'number': this.number,
                'order': this.order,
                'action': this.action
            };

            this.saveProAjaxCall( datos, api );

        }

        // Remove interpretation for tipo number.
        if ( e.target.classList.contains( 'remove-inter' ) ) {
 
            e.preventDefault();

            this.loadProParameters(e); // Set values into interpretayion parameters global variables.

            let api = 'save-chart-content';
            this.action = 'remove-inter';
            let datos = {
                'tipo': this.tipo,
                'number': this.number,
                'pro_id': this.proId,
                'order': this.order,                
                'action': this.action
            };

            this.saveProAjaxCall( datos, api );
        }
    }

    /**
     * Skeleton for ajax call.
     * 
     * @param {*} datos array of data to pass with ajax.
     * @param {*} api   API address to sue with the ajax call.
     * @return          result for the ajax call.
     */
    saveProAjaxCall ( datos = [], api = '' ) {

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/'+ api,
            type: 'GET',
            data: datos,
            success: ( data ) => {

                if ( data.response == 'SUCCESS' ) {

                    
                    if ( this.action == 'create-inter' ) {
                        alert( 'Interpretación creada con exito!.');
                        this.modalMannager(); // hide Modal Box.
                        // Load new content using ajax.
                        location.reload();
                    }

                    if ( this.action == 'remove-inter' ) {
                        alert( 'La Interpretacion fue exitosamente borrada del pronostico.' );
                        // borrar texto de la interpretacion.
                        location.reload();
                    }

                    if ( this.action == 'load-options' ) {
                        let modal = this.modalBox( data );
                        let body = document.body;
                        body.insertBefore( modal, body.lastChild );
                        // Listen for change in the select field.
                        this.listenSelect();

                    }

                    if ( this.action == 'save-inter-id' ) {
                        location.reload();
                    }

                    if ( this.action == 'save-pro-content' ) {
                        location.reload();
                    }

                    if ( this.action == 'reset-pro-content' ) {
                        location.reload();
                    }

                } else {
                    console.log( data );
                }
                
            },
            error: (data) => {
                console.log( data );
            },           
        });  
 
    }

    /**
     * Listen when one of the interpretation options is selected.
     * 
     * @param {*} e 
     */
    listenSelect() {
        if ( document.querySelector( '.select-inter' ) ) {
            document.querySelector( '.select-inter' ).addEventListener( 'change', this.setInterToPro.bind( this) );
        }        
    }

    setInterToPro(e) {
        this.action = 'save-inter-id';
        let api = 'save-chart-content';

        let interId = e.target.options[e.target.selectedIndex].value;

        console.log( interId, this.proId, this.order, this.number, this.tipo );

        let datos = {
            'inter_id': interId,
            'order': this.order,
            'number': this.number,
            'tipo': this.tipo,
            'pro_id': this.proId,
            'action': this.action,
        }

        this.saveProAjaxCall( datos, api );

    }

    /**
     * Set global variables values related to current interpretation.
     * 
     * @param {*} e target element.
     */
    loadProParameters(e) {
        let numberBox = e.target.parentNode.parentNode.parentNode;
        this.tipo = numberBox.getAttribute( 'data-tipo' );
        this.number = numberBox.getAttribute( 'data-number' );
        this.order = numberBox.getAttribute( 'data-order' );
    }

    /**
     * Shows/Hides Modal Box.
     */
    modalMannager() {
        console.log('hola');
        if ( ! this.modalStatus ) {
            document.querySelector( '.editor-box' ).classList.add( 'active' );
            this.modalStatus = true;
            document.querySelector( "[name='inter-title']" ).value = this.tipo +' '+ this.number;
            return;            
        } else {
            document.querySelector( '.editor-box' ).classList.remove( 'active' );
            this.modalStatus = false; 
            return;
        }

    }
    /**
     * Creates HTML for modal the box.
     * 
     * @param {*} data array with values returned from an ajax call, to use into the modal box.
     */
    modalBox( data ) {
        let el = document.createElement( 'div' );
        el.classList.add( 'num-modal-box' );
        let out = `
            <div class="modal-box__inner">
                <div class="modal-box__header">
                    <span class="btn btn--close">Cerrar</span>
                </div>
                <div class="modal-box__body">
                    ${ data.html }
                </div>
            </div>
        `;
        el.innerHTML = out;
        return el;
    }
}