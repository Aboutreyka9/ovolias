const FormValidator = {
    patterns: {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        phone: /^[0-9]{10}$/,
        number: /^\d+$/,
        password: /^.{6,}$/
    },

    cleanPhone: function(val) {
        if (!val) return '';
        let cleaned = String(val).replace(/[^0-9]/g, '');
        if (cleaned.startsWith('00225')) {
            cleaned = cleaned.substring(5);
        } else if (cleaned.startsWith('225') && cleaned.length > 10) {
            cleaned = cleaned.substring(3);
        }
        return cleaned.substring(0, 10);
    },

    icons: {
        success: '<i class="fa fa-check-circle" style="color: #28a745;"></i>',
        error: '<i class="fa fa-exclamation-circle" style="color: #dc3545;"></i>'
    },

    config: {
        utilisateur: {
            nom: { required: true, minLength: 2 },
            email: { required: true, pattern: 'email' },
            telephone: { required: true, pattern: 'phone' }
        },
        client: {
            nom: { required: true, minLength: 2 },
            email: { required: true, pattern: 'email' },
            telephone: { required: true, pattern: 'phone' }
        },
        product: {
            libelle: { required: true, minLength: 2 },
            code: { required: true, minLength: 2 }
        },
        category: {
            libelle: { required: true, minLength: 2 }
        },
        fournisseur: {
            nom: { required: true, minLength: 2 },
            telephone: { required: true, pattern: 'phone' }
        },
        livreur: {
            nom: { required: true, minLength: 2 },
            telephone: { required: true, pattern: 'phone' }
        },
        order: {
            client_code: { required: true },
            montant_total: { required: true, pattern: 'number' }
        },
        paiement: {
            commande_code: { required: true },
            montant: { required: true, pattern: 'number' }
        },
        role: {
            nom: { required: true, minLength: 2 }
        },
        permission: {
            module: { required: true },
            action: { required: true }
        },
        stock: {
            produit_code: { required: true },
            quantite: { required: true, pattern: 'number' }
        },
        commission: {
            commande_code: { required: true },
            montant: { required: true, pattern: 'number' }
        },
        setting: {
            libelle: { required: true },
            valeur: { required: true }
        },
        city: {
            code: { required: true, minLength: 2 },
            libelle: { required: true, minLength: 2 }
        }
    },

    getFieldError: function(input, rules) {
        const value = input.value.trim();
        
        if (rules.required && value === '') {
            return 'Ce champ est requis';
        }
        if (value !== '' && rules.minLength && value.length < rules.minLength) {
            return `Minimum ${rules.minLength} caractères requis`;
        }
        if (value !== '' && rules.pattern) {
            if (!this.patterns[rules.pattern].test(value)) {
                const labels = { email: 'email', phone: 'téléphone', number: 'nombre', password: 'mot de passe' };
                return `Format ${labels[rules.pattern] || 'invalide'} requis`;
            }
        }
        return null;
    },

    validateField: function(input, useConfig = true) {
        const field = input.name;
        const form = input.closest('form');
        const entityType = form?.dataset?.entity || input.dataset.entity;
        const rules = useConfig && entityType ? this.config[entityType]?.[field] : null;
        
        const effectiveRules = rules || {
            required: input.hasAttribute('required'),
            pattern: input.type === 'email' ? 'email' : input.type === 'number' ? 'number' : input.type === 'tel' ? 'phone' : null
        };

        const error = this.getFieldError(input, effectiveRules);
        
        this.showFieldFeedback(input, error === null, error);
        
        return error === null;
    },

    showFieldFeedback: function(input, isValid, errorMessage = null) {
        if (!input) return;
        input.style.borderColor = isValid ? '#28a745' : '#dc3545';
        
        const wrapper = input.closest('.input-wrapper');
        let feedbackIcon = wrapper?.querySelector('.field-feedback-icon');
        
        if (feedbackIcon) {
            feedbackIcon.classList.toggle('show', true);
            feedbackIcon.innerHTML = isValid ? this.icons.success : this.icons.error;
        }
        
        const group = input.closest('.form-group') || input.parentElement;
        
        let errorDisplay = group.querySelector('.field-error-msg');
        if (!isValid && errorMessage) {
            if (!errorDisplay) {
                errorDisplay = document.createElement('small');
                errorDisplay.className = 'field-error-msg';
                if (wrapper) {
                    wrapper.parentNode.insertBefore(errorDisplay, wrapper.nextSibling);
                } else {
                    input.parentNode.insertBefore(errorDisplay, input.nextSibling);
                }
            }
            errorDisplay.textContent = errorMessage;
            errorDisplay.classList.add('show');
        } else if (errorDisplay) {
            errorDisplay.classList.remove('show');
        }
    },

    validateForm: function(form) {
        const entityType = form.dataset.entity;
        const rules = entityType ? this.config[entityType] : null;
        
        if (!rules) {
            return this.validateFormBasic(form);
        }

        let isValid = true;
        Object.keys(rules).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            }
        });
        return isValid;
    },

    validateFormBasic: function(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input, false)) {
                isValid = false;
            }
        });
        return isValid;
    },

    initForModal: function(form, entityType) {
        if (!form) return;
        
        form.dataset.entity = entityType;

        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input, true));
            input.addEventListener('input', () => {
                if (input.value.length > 0) {
                    this.validateField(input, true);
                }
            });
        });
    },

    initRealTime: function(formSelector) {
        const form = typeof formSelector === 'string' ? document.querySelector(formSelector) : formSelector;
        if (!form) return;

        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input, true));
            input.addEventListener('input', () => {
                if (input.value.length > 0) {
                    this.validateField(input, true);
                }
            });
        });
    }
};

window.FormValidator = FormValidator;

// Auto-nettoyage des numéros de téléphone (retrait immédiat de +225, 225 et espaces)
document.addEventListener('input', function(e) {
    const target = e.target;
    if (target && (target.type === 'tel' || (target.name && (target.name.includes('telephone') || target.name.includes('contact'))))) {
        if (target.value && (target.value.includes('+') || target.value.startsWith('225') || target.value.includes(' ') || target.value.includes('-') || target.value.includes('.'))) {
            target.value = FormValidator.cleanPhone(target.value);
        }
    }
});

document.addEventListener('blur', function(e) {
    const target = e.target;
    if (target && (target.type === 'tel' || (target.name && (target.name.includes('telephone') || target.name.includes('contact'))))) {
        if (target.value) {
            target.value = FormValidator.cleanPhone(target.value);
        }
    }
}, true);