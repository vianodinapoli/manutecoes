
// resources/js/app.js

import './bootstrap'; 
import Toastify from 'toastify-js';

// Torna o Toastify global para ser facilmente chamado em Blade (se necessário)
window.Toastify = Toastify; 

document.addEventListener('DOMContentLoaded', () => {
    
    // --- LÓGICA DE SUCESSO ---
    const successMessageElement = document.querySelector('[data-toast-success]');

    if (successMessageElement) {
        Toastify({
            text: successMessageElement.dataset.toastSuccess,
            duration: 5000,
            gravity: "top", 
            position: "right", 
            // Cor verde minimalista (Cor de Ação/Sucesso)
            backgroundColor: "#10B981", 
            stopOnFocus: true, 
        }).showToast();
    }

    // --- LÓGICA DE ERRO/FALHA ---
    const errorMessageElement = document.querySelector('[data-toast-error]');
    
    if (errorMessageElement) {
        Toastify({
            text: errorMessageElement.dataset.toastError,
            duration: 7000, // Dá mais tempo para ler erros
            gravity: "top", 
            position: "right", 
            // Cor vermelha para erros
            backgroundColor: "#EF4444", 
            stopOnFocus: true,
        }).showToast();
    }
});