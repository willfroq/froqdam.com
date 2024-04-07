## Login form
This component is used to render a login form. It is re-usable, although it is currently only meant to be 
used in PIM login page. 
The input field within a div tag that has the attribute "data-role" set to "password_field_control" will 
automatically feature a toggle button that allows to switch visibility of the characters in the password field.

### Sample HTML
```
    <app-login-form class="login-form">
        <div class="login-form__inner-container">
            <h3>
                <span>Log into your account</span>
            </h3>
            <form action="<?= $configuration['base_url']?>/login-response.php"
                  method="POST"
                  class="form mt-xxl">
                <div class="form-field email required">
                    <label for="email">E-mail</label>
                    <div class="control"
                         data-role="email_field_control">
                        <input id="email"
                               name="email"
                               class="input-text"
                               type="text"
                               placeholder="person@hotmail.com"
                        />
                    </div>
                </div>
                <div class="form-field password required mt-s mb-xxl">
                    <label for="password">Password</label>
                    <div class="control"
                         data-role="password_field_control">
                        <input id="password"
                               class="input-text"
                               type="password"
                               placeholder="Password"
                        />
                    </div>
                    <?php if ($error): ?>
                        <div class="login-form-error-message">
                            <span>Error: Incorrect username or password.</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <button type="submit"
                            class="button button-md button-primary login-form__submit-button">
                        <span>Log in</span>
                    </button>
                </div>
            </form>
        </div>
    </app-login-form>
```

### Dependencies
This component has a dependency with the following components:

- "ui-component"
