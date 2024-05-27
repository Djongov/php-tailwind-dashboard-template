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

        if (data === 'The .env file has been created successfully.') {
            responseDiv.innerText += ` Redirecting to root...`;
            responseDiv.style.color = 'green';
            setInterval(() => {
                window.location = '/';
            }, 2000)
        }
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
        inputField.style.margin = '6px';

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
    toggleAdditionalField('Entra_ID_login', 'AZURE_AD_CLIENT_SECRET', 'Client Secret');
    toggleAdditionalField('Entra_ID_login', 'AZURE_AD_CLIENT_ID', 'App ID');
    toggleAdditionalField('Entra_ID_login', 'AZURE_AD_TENANT_ID', 'Tenant ID');
    // Add a small paragraph with the instructions
    if (document.getElementById('Entra_ID_login').checked) {
        addInstructions('Entra_ID_login', 'Entra_ID_instructions', 'Create a new App registration in Azure AD. Create a secret and copy the values to the fields below. Make sure to add the redirect URI to the App registration. More info in the README.md file.');
    } else {
        removeInstructions('Entra_ID_instructions');
    }

});

// MS Live
document.getElementById('Microsoft_LIVE_login').addEventListener('change', () => {
    toggleAdditionalField('Microsoft_LIVE_login', 'MS_LIVE_CLIENT_SECRET', 'Client Secret');
    toggleAdditionalField('Microsoft_LIVE_login', 'MS_LIVE_CLIENT_ID', 'Client ID');
    toggleAdditionalField('Microsoft_LIVE_login', 'MS_LIVE_TENANT_ID', 'Tenant ID');
    if (document.getElementById('Microsoft_LIVE_login').checked) {
        addInstructions('Microsoft_LIVE_login', 'Microsoft_LIVE_instructions', 'Create a new App registration in Entra ID. Make sure that LIVE accounts are supported. Create a secret and copy the values to the fields below. Make sure to add the redirect URI to the App registration. More info in the README.md file.');
    } else {
        removeInstructions('Microsoft_LIVE_instructions');
    }
});

// Google login
document.getElementById('Google_login').addEventListener('change', () => {
    toggleAdditionalField('Google_login', 'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_ID');
    toggleAdditionalField('Google_login', 'GOOGLE_CLIENT_SECRET', 'GOOGLE_CLIENT_SECRET');

    if (document.getElementById('Google_login').checked) {
        addInstructions('Google_login', 'Google_login_instructions', 'Register new Credentials in GPC. Go to API and Services -> Credentials -> Create Credentials -> OAuth Client ID. Select Web Application and fill the form. Copy the Client ID and Client Secret to the fields below. Make sure to add the redirect URI to the Credentials. More info in the README.md file.');
    } else {
        removeInstructions('Google_login_instructions');
    }
});

const addInstructions = (checkboxId, instructionsId, text) => {
    const existingInstructions = document.getElementById(instructionsId);
    if (existingInstructions) {
        existingInstructions.remove();
    }
    const checkbox = document.getElementById(checkboxId);
    const instructions = document.createElement('p');
    instructions.innerText = text;
    instructions.style.color = 'gray';
    instructions.style.fontSize = '12px';
    instructions.style.margin = '6px';
    instructions.id = instructionsId;
    // Append it after the label of the checkbox
    checkbox.parentNode.insertBefore(instructions, checkbox.nextSibling);
}

const removeInstructions = (instructionsId) => {    
    const existingInstructions = document.getElementById(instructionsId);
    if (existingInstructions) {
        existingInstructions.remove();
    }
}

