export default class Interpretaciones_Mannager {
    
    constructor() {
        this.btnAdd = 'add-inter';
        this.modalStatus = false;
        this.tipo = '';
        this.number = '';
        this.desOrder = '';
        this.proId = '';
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
    }

    listenSelect() {
        if ( document.querySelector( '.select-inter' ) ) {
            document.querySelector( '.select-inter' ).addEventListener( 'change', this.setInterToPro.bind( this) );
        }        
    }

    setInterToPro(e) {

        let datos = {
            'inter_id': e.target.value ? e.target.value : '',
            'order': this.desOrder ? this.desOrder : '',
            'number': this.number,
            'tipo': this.tipo,
            'pro_id': this.proId,
            'action': 'save-inter-id',
        }

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/save-chart-content',
            type: 'GET',
            data: datos,
            success: (data) => {
                console.log(data);
                if (data.response == 'SUCCESS' ) {
                    console.log(data);
                    //location.reload();
                } else {
                    console.log('error');
                }
                
            },
            error: (data) => {
                console.log(data);
            },           
        }); 

    }

    btnClicked(e) {
 
        if ( e.target.classList.contains( 'btn--close' ) ) {
            document.querySelector( '.num-modal-box' ).remove();
        }

        if ( e.target.classList.contains( 'btn--select-inter' ) ) {
            e.preventDefault();
            
            this.tipo = e.target.getAttribute( 'data-tipo' );
            this.number = e.target.getAttribute( 'data-number' );
            this.desOrder = e.target.getAttribute( 'data-order' );
  
            let datos = {
                'tipo': this.tipo,
                'numero': this.number,
                'order': this.desOrder,
                'action': 'load-options', 
            }

            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
                },
                url: chartsData.root_url + '/wp-json/charts-json/v1/save-chart-content',
                type: 'GET',
                data: datos,
                success: (data) => {
                    if (data.response == 'SUCCESS' ) {
                         let modal = this.modalBox( data );
                        let body = document.body;
                        body.insertBefore( modal, body.lastChild );
                        this.listenSelect();
                        //location.reload();
                    } else {
                        console.log('error');
                    }
                    
                },
                error: (data) => {
                    console.log(data);
                },           
            });             
        }

        if ( e.target.classList.contains( 'editor-box--close' ) ) {
            document.querySelector( '.editor-box' ).classList.remove( 'active' );
            this.modalStatus = false;             
        }

        if ( e.target.classList.contains( this.btnAdd ) ) {
            e.preventDefault();
            if ( ! this.modalStatus ) {
                document.querySelector( '.editor-box' ).classList.add( 'active' );
                this.modalStatus = true;
                this.tipo = e.target.getAttribute( 'data-tipo' );
                this.number = e.target.getAttribute( 'data-number' );
                this.desOrder = e.target.getAttribute( 'data-order' );
                document.querySelector( "[name='inter-title']" ).value = this.tipo +' '+ this.number;
                return;
            } else {
                document.querySelector( '.editor-box' ).classList.remove( 'active' );
                this.modalStatus = false; 
                return;               
            }

        }

        if ( e.target.classList.contains( 'save-content' ) ) {
            e.preventDefault();

            this.ajaxCreator();
        }
    }

    ajaxCreator() {
        let title = document.querySelector("[name='inter-title']").value;
        let content = tinymce.activeEditor.getContent();
        let datos = {
            'tipo': this.tipo,
            'number': this.number,
            'content':  content,
            'title': title,
            'order': this.desOrder,
            'pro_id': this.proId,
            'order': this.desOrder,
            'action': 'create-inter'
        }

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', chartsData.nonce);
            },
            url: chartsData.root_url + '/wp-json/charts-json/v1/save-chart-content',
            type: 'GET',
            data: datos,
            success: (data) => {
                if (data.response == 'SUCCESS' ) {
                    console.log(data);
                    // Save inter to pronostico.
                    //location.reload();
                } else {
                    console.log('error');
                }
                
            },
            error: (data) => {
                console.log(data);
            },           
        });        
    }

    saveContent(e) {
        console.log(e.target);
    }

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