/**
 * Biometric Authentication Helper
 * 
 * Este archivo contiene funciones de utilidad para implementar la autenticación
 * biométrica en aplicaciones web utilizando la API Web Authentication.
 */

const BiometricAuth = {
    /**
     * Comprueba si el navegador soporta autenticación biométrica (WebAuthn)
     * @returns {boolean} - true si el navegador soporta WebAuthn
     */
    isSupported: function() {
        return window.PublicKeyCredential !== undefined;
    },
    
    /**
     * Inicia el proceso de registro de credenciales biométricas
     * @param {string} url - URL para la solicitud de registro
     * @param {Object} data - Datos para enviar en la solicitud
     * @param {function} onSuccess - Callback a ejecutar en caso de éxito
     * @param {function} onError - Callback a ejecutar en caso de error
     */
    startRegistration: function(url, data, onSuccess, onError) {
        // Comprobar soporte primero
        if (!this.isSupported()) {
            if (onError) onError(new Error('Tu navegador no admite autenticación biométrica. Por favor, utiliza un navegador más reciente.'));
            return;
        }
        
        // Realizar petición para obtener opciones de creación
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                throw new Error(data.message || 'Error desconocido al iniciar el registro');
            }
            
            // Preparar opciones para navigator.credentials.create()
            const createOptions = this._preparePublicKeyCreationOptions(data.options);
            
            // Crear credencial
            return navigator.credentials.create({
                publicKey: createOptions
            });
        })
        .then(credential => {
            // Formatear la respuesta de credencial
            const formattedResponse = this._formatCredentialResponse(credential);
            
            // Pasar al callback de éxito
            if (onSuccess) onSuccess(formattedResponse);
        })
        .catch(error => {
            console.error('Error en el registro biométrico:', error);
            if (onError) onError(error);
        });
    },
    
    /**
     * Inicia el proceso de autenticación utilizando biometría
     * @param {string} url - URL para la solicitud de autenticación
     * @param {Object} data - Datos para enviar en la solicitud
     * @param {function} onSuccess - Callback a ejecutar en caso de éxito
     * @param {function} onError - Callback a ejecutar en caso de error
     */
    startAuthentication: function(url, data, onSuccess, onError) {
        // Comprobar soporte primero
        if (!this.isSupported()) {
            if (onError) onError(new Error('Tu navegador no admite autenticación biométrica. Por favor, utiliza un navegador más reciente.'));
            return;
        }
        
        // Realizar petición para obtener opciones de autenticación
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                throw new Error(data.message || 'Error desconocido al iniciar la autenticación');
            }
            
            // Preparar opciones para navigator.credentials.get()
            const getOptions = this._preparePublicKeyRequestOptions(data.options);
            
            // Obtener credencial
            return navigator.credentials.get({
                publicKey: getOptions
            });
        })
        .then(credential => {
            // Formatear la respuesta de autenticación
            const formattedResponse = this._formatAuthResponse(credential);
            
            // Pasar al callback de éxito
            if (onSuccess) onSuccess(formattedResponse);
        })
        .catch(error => {
            console.error('Error en la autenticación biométrica:', error);
            if (onError) onError(error);
        });
    },
    
    /**
     * Prepara las opciones para la creación de credenciales
     * @private
     * @param {Object} options - Opciones recibidas del servidor
     * @returns {Object} - Opciones formateadas para WebAuthn
     */
    _preparePublicKeyCreationOptions: function(options) {
        // Convertir challenge de base64 a ArrayBuffer
        if (options.challenge) {
            options.challenge = this._base64ToArrayBuffer(options.challenge);
        }
        
        // Convertir user.id de base64 a ArrayBuffer
        if (options.user && options.user.id) {
            options.user.id = this._base64ToArrayBuffer(options.user.id);
        }
        
        // Convertir excludeCredentials.id de base64 a ArrayBuffer
        if (options.excludeCredentials) {
            options.excludeCredentials = options.excludeCredentials.map(credential => {
                return {
                    ...credential,
                    id: this._base64ToArrayBuffer(credential.id)
                };
            });
        }
        
        return options;
    },
    
    /**
     * Prepara las opciones para la solicitud de credenciales
     * @private
     * @param {Object} options - Opciones recibidas del servidor
     * @returns {Object} - Opciones formateadas para WebAuthn
     */
    _preparePublicKeyRequestOptions: function(options) {
        // Convertir challenge de base64 a ArrayBuffer
        if (options.challenge) {
            options.challenge = this._base64ToArrayBuffer(options.challenge);
        }
        
        // Convertir allowCredentials.id de base64 a ArrayBuffer
        if (options.allowCredentials) {
            options.allowCredentials = options.allowCredentials.map(credential => {
                return {
                    ...credential,
                    id: this._base64ToArrayBuffer(credential.id)
                };
            });
        }
        
        return options;
    },
    
    /**
     * Formatea la respuesta de la credencial para enviar al servidor
     * @private
     * @param {PublicKeyCredential} credential - Credencial generada
     * @returns {Object} - Respuesta formateada
     */
    _formatCredentialResponse: function(credential) {
        const { id, rawId, response, type } = credential;
        
        return {
            id: id,
            rawId: this._arrayBufferToBase64(rawId),
            response: {
                clientDataJSON: this._arrayBufferToBase64(response.clientDataJSON),
                attestationObject: this._arrayBufferToBase64(response.attestationObject)
            },
            type: type
        };
    },
    
    /**
     * Formatea la respuesta de autenticación para enviar al servidor
     * @private
     * @param {PublicKeyCredential} credential - Credencial de autenticación
     * @returns {Object} - Respuesta formateada
     */
    _formatAuthResponse: function(credential) {
        const { id, rawId, response, type } = credential;
        
        return {
            id: id,
            rawId: this._arrayBufferToBase64(rawId),
            response: {
                clientDataJSON: this._arrayBufferToBase64(response.clientDataJSON),
                authenticatorData: this._arrayBufferToBase64(response.authenticatorData),
                signature: this._arrayBufferToBase64(response.signature),
                userHandle: response.userHandle ? this._arrayBufferToBase64(response.userHandle) : null
            },
            type: type
        };
    },
    
    /**
     * Convierte un ArrayBuffer a string Base64
     * @private
     * @param {ArrayBuffer} buffer - Buffer a convertir
     * @returns {string} - String Base64
     */
    _arrayBufferToBase64: function(buffer) {
        const binary = String.fromCharCode.apply(null, new Uint8Array(buffer));
        return window.btoa(binary);
    },
    
    /**
     * Convierte un string Base64 a ArrayBuffer
     * @private
     * @param {string} base64 - String Base64 a convertir
     * @returns {ArrayBuffer} - ArrayBuffer resultante
     */
    _base64ToArrayBuffer: function(base64) {
        const binary = window.atob(base64);
        const buffer = new ArrayBuffer(binary.length);
        const bytes = new Uint8Array(buffer);
        
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        
        return buffer;
    }
};

// Exportar para entornos con módulos
if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = BiometricAuth;
}
