"use strict";

// Select2 (jquery)
$(function () {
   var select2 = $(".select2");
   if (select2.length) {
      select2.each(function () {
         var $this = $(this);
         $this.wrap('<div class="position-relative"></div>');
         $this.select2({
            placeholder: "Select an country",
            dropdownParent: $this.parent(),
         });
      });
   }
});

// Multi Steps Validation
document.addEventListener("DOMContentLoaded", function (e) {
   (function () {
      const stepsValidation = document.querySelector("#multiStepsValidation");
      if (typeof stepsValidation !== "undefined" && stepsValidation !== null) {
         const stepsValidationForm =
            stepsValidation.querySelector("#multiStepsForm");
         const stepsValidationFormStep1 = stepsValidationForm.querySelector(
            "#accountDetailsValidation"
         );
         const stepsValidationFormStep2 = stepsValidationForm.querySelector(
            "#personalInfoValidation"
         );

         const stepsValidationNext = [].slice.call(
            stepsValidationForm.querySelectorAll(".btn-next")
         );
         const stepsValidationPrev = [].slice.call(
            stepsValidationForm.querySelectorAll(".btn-prev")
         );

         let validationStepper = new Stepper(stepsValidation, {
            linear: true,
         });

         // Account details validation
         const multiSteps1 = FormValidation.formValidation(
            stepsValidationFormStep1,
            {
               fields: {
                  name: {
                     validators: {
                        notEmpty: { message: "Please enter your full name" },
                        stringLength: {
                           min: 6,
                           message:
                              "The name must be at least 6 characters long",
                        },
                        regexp: {
                           regexp: /^[a-zA-Z0-9 ]+$/,
                           message:
                              "The name can only consist of alphabetical, number and space",
                        },
                     },
                  },
                  email: {
                     validators: {
                        notEmpty: { message: "Please enter email address" },
                        emailAddress: {
                           message: "The value is not a valid email address",
                        },
                     },
                  },
                  password: {
                     validators: {
                        // notEmpty: { message: "Please enter password" },
                        callback: {
                           callback: function (input) {
                              const value = input.value;
                              if (value === "")
                                 return {
                                    valid: false,
                                    message: "Please enter password",
                                 };
                              if (value.length < 8)
                                 return {
                                    valid: false,
                                    message:
                                       "The password must be at least 8 characters long",
                                 };
                              if (
                                 !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).+$/.test(
                                    value
                                 )
                              ) {
                                 return {
                                    valid: false,
                                    message:
                                       "The password must contain at least one uppercase, one numeric, and one special character",
                                 };
                              }
                              return { valid: true };
                           },
                        },
                     },
                  },
                  confirm_password: {
                     validators: {
                        notEmpty: { message: "Confirm Password is required" },
                        identical: {
                           compare: function () {
                              return stepsValidationFormStep1.querySelector(
                                 "[name=password]"
                              ).value;
                           },
                           message:
                              "The password and its confirm are not the same",
                        },
                     },
                  },
               },
               plugins: {
                  trigger: new FormValidation.plugins.Trigger(),
                  bootstrap5: new FormValidation.plugins.Bootstrap5({
                     eleValidClass: "",
                     rowSelector: ".form-control-validation",
                  }),
                  autoFocus: new FormValidation.plugins.AutoFocus(),
                  submitButton: new FormValidation.plugins.SubmitButton(),
               },
               init: (instance) => {
                  instance.on("plugins.message.placed", function (e) {
                     if (
                        e.element.parentElement.classList.contains(
                           "input-group"
                        )
                     ) {
                        e.element.parentElement.insertAdjacentElement(
                           "afterend",
                           e.messageElement
                        );
                     }
                  });
               },
            }
         ).on("core.form.valid", function () {
            validationStepper.next();
         });

         // Personal info validation
         const multiSteps2 = FormValidation.formValidation(
            stepsValidationFormStep2,
            {
               fields: {
                  gender: {
                     validators: {
                        notEmpty: { message: "Please select your gender" },
                     },
                  },
                  // date_of_birth: {
                  //    validators: {
                  //       notEmpty: {
                  //          message: "Please enter your date of birth",
                  //       },
                  //       date: {
                  //          format: "YYYY-MM-DD",
                  //          message: "The date of birth is not valid",
                  //       },
                  //    },
                  // },
                  phone_number: {
                     validators: {
                        notEmpty: { message: "Please enter your phone number" },
                        regexp: {
                           regexp: /^\+\d{9,14}$/,
                           message:
                              "Phone number must be in international format (e.g., +233501234567)",
                        },
                     },
                  },
                  address: {
                     validators: {
                        notEmpty: { message: "Please enter your address" },
                        stringLength: {
                           min: 10,
                           message:
                              "Address must be at least 10 characters long",
                        },
                     },
                  },
                  emergency_contact_name: {
                     validators: {
                        notEmpty: {
                           message: "Please enter emergency contact name",
                        },
                        stringLength: {
                           min: 3,
                           message:
                              "Emergency contact name must be at least 3 characters long",
                        },
                     },
                  },
                  emergency_contact_number: {
                     validators: {
                        notEmpty: {
                           message: "Please enter emergency contact number",
                        },
                        regexp: {
                           regexp: /^\+\d{9,14}$/,
                           message:
                              "Emergency contact number must be in international format (e.g., +233501234567)",
                        },
                     },
                  },
               },
               plugins: {
                  trigger: new FormValidation.plugins.Trigger(),
                  bootstrap5: new FormValidation.plugins.Bootstrap5({
                     eleValidClass: "",
                     rowSelector: ".form-control-validation",
                  }),
                  autoFocus: new FormValidation.plugins.AutoFocus(),
                  submitButton: new FormValidation.plugins.SubmitButton(),
               },
            }
         ).on("core.form.valid", function () {
            stepsValidationForm.submit();
         });

         // Handle Next button clicks
         stepsValidationNext.forEach((item) => {
            item.addEventListener("click", (event) => {
               switch (validationStepper._currentIndex) {
                  case 0:
                     multiSteps1.validate().then((result) => {
                        if (result === "Valid") validationStepper.next();
                     });
                     break;
                  case 1:
                     multiSteps2.validate().then((result) => {
                        if (result === "Valid") stepsValidationForm.submit();
                     });
                     break;
                  default:
                     break;
               }
            });
         });

         // Handle Previous button clicks
         stepsValidationPrev.forEach((item) => {
            item.addEventListener("click", () => {
               validationStepper.previous();
            });
         });
      }
   })();
});
