let languagesContainer;
let educationContainer;
let linksContainer;

window.onload = () => {
    languagesContainer = document.getElementById("languages");
    educationContainer = document.getElementById("education");
    linksContainer = document.getElementById("links");

    languagesContainer.querySelectorAll("#delete-language").forEach((button) => {
        handleDelete(button, languagesContainer, true)
    })

    educationContainer.querySelectorAll("#delete-education").forEach((button) => {
        handleDelete(button, educationContainer, false)
    })

    linksContainer.querySelectorAll("#delete-link").forEach((button) => {
        handleDelete(button, linksContainer, false)
    })

    if (languagesContainer.children.length === 0) {
        addLanguageEntry("", true)
    }
    if (educationContainer.children.length === 0) {
        addEducationEntry()
    }
    if (linksContainer.children.length === 0) {
        addLinkEntry()
    }

    document.getElementById('add-language').addEventListener("click", () => {
        addLanguageEntry()
    });

    document.getElementById('add-education').addEventListener("click", () => {
        addEducationEntry()
    });

    document.getElementById('add-link').addEventListener("click", () => {
        addLinkEntry()
    });
};

const handleDelete = (button, container, required) => {
    button.addEventListener("click", () => {
        if (required && container.children.length === 1) {
            button.checked = false;
            return;
        }
        button.parentElement.parentElement.parentElement.remove();
    })
}

const addLanguageEntry = (name = "", isKey = false) => {
    const index = languagesContainer.children.length;

    const languageEntry = document.createElement("div");
    languageEntry.className = "language-entry";

    languageEntry.innerHTML = `
        <input name="languages[]" class="input section-input" placeholder="Language Name" value="${name}" />
                <div class="options">
                    <div class="option">
                        <input type="radio" name="key_language" value="${index}" ${isKey ? "checked" : ""}>
                        <label for="key_language">Is Key Language</label>
                    </div>
                    <div class="option">
                        <label for="delete-language">Delete</label>
                        <input type="checkbox" id="delete-language" />
                    </div>
                </div>
    `;

    languagesContainer.appendChild(languageEntry);
    handleDelete(languageEntry.querySelector('#delete-language'), languagesContainer);
}

const addEducationEntry = (name = "") => {
    const educationEntry = document.createElement("div");
    educationEntry.className = "education-entry";

    educationEntry.innerHTML = `
       <div class="education-entry">
                            <input name="education[]" class="input section-input" placeholder="Institution Name" value="${name}" />
                            <div class="options">
                                <div class="option">
                                    <label for="delete-language">Delete</label>
                                    <input type="checkbox" id="delete-education" />
                                </div>
                            </div>
                        </div>
    `;

    educationContainer.appendChild(educationEntry);
    handleDelete(educationEntry.querySelector('#delete-education'), educationContainer, false);
}


const addLinkEntry = (name = "") => {
    const linkEntry = document.createElement("div");
    linkEntry.className = "link-entry";

    linkEntry.innerHTML = `
        <input name="links[]" class="input section-input" placeholder="Link" value="${name}" />
        <div class="options">
            <div class="option">
                <label for="delete-link">Delete</label>
                <input type="checkbox" id="delete-link" />
            </div>
        </div>
    `;

    linksContainer.appendChild(linkEntry);
    handleDelete(linkEntry.querySelector('#delete-link'), linksContainer, false);
}