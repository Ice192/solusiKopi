"use strict";

document.addEventListener("DOMContentLoaded", function () {
    (function () {
        const validateForm = (formSelector, fieldsConfig, rowSelector = ".col-md-6") => {
            const formElement = document.querySelector(formSelector);
            if (!formElement) return;

            const activeFields = Object.fromEntries(
                Object.entries(fieldsConfig).filter(([fieldName]) => {
                    return formElement.querySelector(`[name="${fieldName}"]`) !== null;
                })
            );

            const formValidationInstance = FormValidation.formValidation(formElement, {
                fields: activeFields,
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        eleValidClass: "",
                        rowSelector: rowSelector,
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    autoFocus: new FormValidation.plugins.AutoFocus(),
                },
                init: (instance) => {
                    instance.on("plugins.message.placed", function (e) {
                        if (e.element.parentElement.classList.contains("input-group")) {
                            e.element.parentElement.insertAdjacentElement("afterend", e.messageElement);
                        }
                    });

                    // Add `is-valid` class to valid fields
                    instance.on('core.element.validated', function (e) {
                        if (e.valid) {
                            e.element.classList.add('is-valid');
                        }
                    });

                    // Manually submit the form after validation
                    instance.on('core.form.valid', function () {
                        formElement.submit();
                    });
                },
            });
        };

        // Form validation for Authentication
        validateForm("#formAuthentication", {
            name: {
                validators: {
                    notEmpty: {
                        message: "Please enter your name",
                    },
                },
            },
            email: {
                validators: {
                    notEmpty: {
                        message: "Please enter your email",
                    },
                    emailAddress: {
                        message: "Please enter a valid email address",
                    },
                },
            },
            password: {
                validators: {
                    notEmpty: {
                        message: "Please enter your password",
                    },
                    stringLength: {
                        min: 6,
                        message: "Password must be more than 6 characters",
                    },
                },
            },
            password_confirmation: {
                validators: {
                    notEmpty: {
                        message: "Please confirm your password",
                    },
                    identical: {
                        compare: function () {
                            return document.querySelector('#formAuthentication [name="password"]').value;
                        },
                        message: "The password and its confirmation do not match",
                    },
                },
            },
        }, ".mb-5");

        // Additional Example: Two Steps Verification
        const numeralMask = document.querySelectorAll(".numeral-mask");

        if (numeralMask.length) {
            numeralMask.forEach((e) => {
                new Cleave(e, {
                    numeral: true,
                });
            });
        }
    })();
});
