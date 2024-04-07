## Asset information
This component is used to render a collapsible sidebar that lists information related to the current
asset. 

The collapsible sections are expected to have a data-role attribute with value "asset_information_section" and to be 
direct children of the main component tag (app-asset-information).
Each section is expected to have two children:
- A div with data-role attribute set to "asset_information_title". 
This element behaves as a trigger to collapse or expand the current section
- A div with data-role attribute set to "asset_information_content".
This element contains one or multiple DL elements used to list the characteristics of the asset for the 
current section. 

**IMPORTANT** If more than one DL element is found within a the content area only the first DL is shown on page load
and a visibility toggle is added to show the rest of the lists on user click.

### Sample HTML
```
<app-asset-information class="asset-information">
    <div class="asset-information__section" data-role="asset_information_section">
        <div class="asset-information__section-title" data-role="asset_information_section_title" tabindex="0">
            <strong>Asset information</strong>
        </div>
        <div class="asset-information__section-content" data-role="asset_information_section_content">
            <dl>
                <dt>Type</dt>
                <dd>Final Artwork</dd>
                <dt>Status</dt>
                <dd>Approved</dd>
                <dt>Last modified</dt>
                <dd>22-12-2021</dd>
                <dt>Version</dt>
                <dd>2 of 2</dd>
                <dt>Last reviewer</dt>
                <dd>Ajith</dd>
            </dl>
        </div>
    </div>
</app-asset-information>
```

### Dependencies
This component has a dependency with the following components:

- "ui-component"