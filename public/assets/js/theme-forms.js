(function ($) {
    function flatten(value) {
        if (!value) {
            return [];
        }
        if (typeof value === "string") {
            return [value];
        }
        if (Array.isArray(value)) {
            return value.reduce(function (acc, item) {
                return acc.concat(flatten(item));
            }, []);
        }
        return Object.keys(value).reduce(function (acc, key) {
            return acc.concat(flatten(value[key]));
        }, []);
    }

    function formParts($form) {
        return {
            $error: $form.find(".stay-connect-alert--error"),
            $success: $form.find(".stay-connect-success"),
            $fields: $form.find(".stay-connect-fields"),
            $button: $form.find('[type="submit"]'),
            $label: $form.find(".btn-label"),
            $loading: $form.find(".btn-loading")
        };
    }

    function showError($form, html) {
        var parts = formParts($form);
        parts.$success.attr("hidden", true).removeClass("is-visible");
        parts.$error.html(html).removeAttr("hidden").addClass("is-visible");
    }

    function showSuccess($form) {
        var parts = formParts($form);
        parts.$error.attr("hidden", true).removeClass("is-visible").empty();
        parts.$fields.attr("hidden", true).addClass("is-hidden");
        parts.$success.removeAttr("hidden").addClass("is-visible");
        $form.addClass("is-sent");
        $form[0].reset();
    }

    function parseErrors(xhr) {
        var payload = {};
        try {
            payload = xhr.responseJSON || JSON.parse(xhr.responseText || "{}") || {};
        } catch (e) {
            payload = {};
        }

        var messages = flatten(payload.errors || payload.error);
        if (!messages.length && payload.message) {
            messages = [payload.message];
        }
        if (!messages.length) {
            if (xhr.status === 419) {
                messages = ["Your session expired. Refresh the page and try again."];
            } else if (xhr.status === 422) {
                messages = ["Please fill in all required fields."];
            } else {
                messages = ["Something went wrong. Please try again."];
            }
        }
        return messages.join("<br>");
    }

    function setLoading($form, loading) {
        var parts = formParts($form);
        $form.toggleClass("is-loading", loading);
        parts.$button.prop("disabled", loading);
        if (loading) {
            parts.$label.attr("hidden", true);
            parts.$loading.removeAttr("hidden");
        } else {
            parts.$label.removeAttr("hidden");
            parts.$loading.attr("hidden", true);
        }
    }

    $(document).on("submit", "form.js-theme-form", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $(this);
        var parts = formParts($form);

        if (!$form.find('[name="_token"]').length && $("meta[name=csrf-token]").attr("content")) {
            $form.append(
                $("<input>", { type: "hidden", name: "_token", value: $("meta[name=csrf-token]").attr("content") })
            );
        }

        parts.$error.attr("hidden", true).removeClass("is-visible").empty();
        setLoading($form, true);

        $.ajax({
            url: $form.attr("action"),
            method: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN":
                    $form.find('[name="_token"]').val() ||
                    $("meta[name=csrf-token]").attr("content") ||
                    ""
            }
        })
            .done(function (response) {
                if (response && (response.success === true || response.success === "true")) {
                    showSuccess($form);
                    return;
                }
                showError($form, "Something went wrong. Please try again.");
            })
            .fail(function (xhr) {
                showError($form, parseErrors(xhr));
            })
            .always(function () {
                setLoading($form, false);
            });
    });
})(jQuery);
