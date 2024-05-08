const form = document.getElementById('env');

form.action = 'POST';

form.addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent the default form submission
    event.submitter.disabled = true;
    event.submitter.innerHTML = 'loading...';
    const formData = new FormData(form); // Serialize form data
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        event.submitter.innerHTML = 'Submit';
        return response.text(); // Parse response as text
    })
    .then(data => {
        event.submitter.disabled = false;
        // Remove any existing div with id "responseDiv"
        const existingResponseDiv = document.getElementById('responseDiv');
        if (existingResponseDiv) {
            existingResponseDiv.remove();
        }
        // Create a new div element
        const responseDiv = document.createElement('div');
        // Set the id of the div
        responseDiv.id = 'responseDiv';
        // Set the inner HTML of the div to the response text
        responseDiv.innerHTML = data;
        // Append the div to the document body
        document.body.appendChild(responseDiv);
    })
    .catch(error => {
        // Handle errors
        console.error('There was a problem with your fetch operation:', error);
    });
});

function toggleAdditionalField(checkboxId, fieldName, placeholder) {
    const checkbox = document.getElementById(checkboxId);
    const additionalFieldContainer = document.getElementById(`${fieldName}Container`);
    if (checkbox.checked) {
        // If checkbox is checked, create and append the input field
        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.name = fieldName;
        inputField.placeholder = placeholder;
        inputField.required = true; // Optionally make it required
        inputField.id = `${fieldName}Field`;

        // Create a div container for the additional input field
        const fieldContainer = document.createElement('div');
        fieldContainer.id = `${fieldName}Container`;
        fieldContainer.appendChild(inputField);

        // Insert the container below the checkbox
        checkbox.parentNode.insertBefore(fieldContainer, checkbox.nextSibling);
    } else {
        // If checkbox is unchecked, remove the input field container
        if (additionalFieldContainer) {
            additionalFieldContainer.remove();
        }
    }
}

// Add event listener to SENDGRID checkbox
document.getElementById('SENDGRID').addEventListener('change', () => {
    toggleAdditionalField('SENDGRID', 'SENDGRID_API_KEY', 'SendGrid API Key');
});

// Add event listener to Entra ID Login
document.getElementById('Entra_ID_login').addEventListener('change', () => {
    toggleAdditionalField('Entra_ID_login', 'AZURE_AD_CLIENT_ID', 'App ID');
    toggleAdditionalField('Entra_ID_login', 'AZURE_AD_TENANT_ID', 'Tenant ID');
});

