
          <article class="container">
            <form action="{$formUrl|htmlentities}" enctype="multipart/form-data" method="post">
              <a href="#" onclick="updateItems()">&lt; Verderwinkelen</a>
              <h2 class="title is-2">Klantgegevens invullen</h2>

              <div class="card">
                <div class="columns is-multiline">
                  <div class="column is-12">&nbsp;</div>
                  <div class="card-content column is-1">&nbsp;</div>
                  <div class="card-content column {option:oHasErrors}is-5{/option:oHasErrors}{option:oHasNoErrors}is-10{/option:oHasNoErrors} leftContent">
                    <div class="field">
                      <label for="txtFirstName" class="label">Voornaam:</label>
                      <div class="control">
                        <input type="text" id="txtFirstName" name="txtFirstName" value="{$txtFirstName|htmlentities}" placeholder="Voornaam" class="input{option:oFirstNameError} erroneous{/option:oFirstNameError}" />
                      </div>
                    </div>

                    <div class="field">
                      <label for="txtLastName" class="label">Achternaam:</label>
                      <div class="control">
                        <input type="text" id="txtLastName" name="txtLastName" value="{$txtLastName|htmlentities}" placeholder="Achternaam" class="input{option:oLastNameError} erroneous{/option:oLastNameError}" />
                      </div>
                    </div>

                    <div class="field">
                      <label for="txtMail" class="label">E-mail:</label>
                      <div class="control">
                        <input type="text" id="txtMail" name="txtMail" value="{$txtMail|htmlentities}" placeholder="E-mail" class="input{option:oMailError} erroneous{/option:oMailError}" />
                      </div>
                    </div>

                    <div class="field">
                      <label for="txtPhone" class="label">Telefoonnummer:</label>
                      <div class="control">
                        <input type="text" id="txtPhone" name="txtPhone" value="{$txtPhone|htmlentities}" placeholder="Telnr" class="input{option:oPhoneError} erroneous{/option:oPhoneError}" />
                      </div>
                    </div>

                    <div class="field">
                      <label for="txtAddress" class="label">Straat + huisnummer:</label>
                      <div class="control">
                        <input type="text" id="txtAddress" name="txtAddress" value="{$txtAddress|htmlentities}" placeholder="Straat+huisnr" class="input{option:oAddressError} erroneous{/option:oAddressError}" />
                      </div>
                    </div>

                    <div class="field">
                      <label for="txtPostal" class="label">Postcode + woonplaats:</label>
                      <div class="control">
                        <input type="text" id="txtPostal" name="txtPostal" value="{$txtPostal|htmlentities}" placeholder="Postcode" class="input{option:oPostalError} erroneous{/option:oPostalError}" /><input type="text" id="txtCity" name="txtCity" value="{$txtCity|htmlentities}" placeholder="Woonplaats" class="input{option:oCityError} erroneous{/option:oCityError}" />
                      </div>
                    </div>

                    <div class="field">
                      <label for="chkMultiAddress" class="label">Apart facturatieadres?</label>
                      <div class="control">
                        <input type="checkbox" id="chkMultiAddress" name="chkMultiAddress" value="Apart facturatieadres" onclick="chkMultiChecked(this);" {option:oExtraChecked}checked {/option:oExtraChecked}/>
                      </div>
                    </div>

                    <div class="field hideable {option:oExtraHidden} hidden{/option:oExtraHidden}">
                      <label for="txtAddressExtra" class="label">Straat + huisnummer facturatieadres:</label>
                      <div class="control">
                        <input type="text" id="txtAddressExtra" name="txtAddressExtra" value="{$txtAddressExtra|htmlentities}" placeholder="Straat+huisnr" class="input{option:oAddressExtraError} erroneous{/option:oAddressExtraError}" />
                      </div>
                    </div>

                    <div class="field hideable {option:oExtraHidden} hidden{/option:oExtraHidden}">
                      <label for="txtPostalExtra" class="label">Postcode + woonplaats facturatieadres:</label>
                      <div class="control">
                        <input type="text" id="txtPostalExtra" name="txtPostalExtra" value="{$txtPostalExtra|htmlentities}" placeholder="Postcode" class="input{option:oPostalExtraError} erroneous{/option:oPostalExtraError}" /><input type="text" id="txtCityExtra" name="txtCityExtra" value="{$txtCityExtra|htmlentities}" placeholder="Woonplaats" class="input{option:oCityExtraError} erroneous{/option:oCityExtraError}" />
                      </div>
                    </div>
                  </div>

                  {option:oHasErrors}
                  <div class="column is-5 rightContent">
                    <ul id="errors" class="notification is-danger">
                      <li>{$formErrors|htmlentities}</li>
                      {iteration:iErrorSpecifics}
                      <li>{$errorSpecific|htmlentities}</li>
                      {/iteration:iErrorSpecifics}
                    </ul>
                  </div>
                  {/option:oHasErrors}
                </div>

                <footer class="card-footer">
                  <div class="control card-footer-item">
                    <button class="button is-info" id="btnCheckout" name="action" value="checkout">Afrekenen</button>
                  </div>

                  <div class="control card-footer-item">
                    <button class="button is-light" id="btnUpdate" name="action" value="updateAndBack">Terug</button>
                  </div>
                </footer>

                <div class="is-hidden">
                  <label for="btnBrowse"><button id="btnBrowse" name="action" value="continueBrowsing">Verderwinkelen</button></label>
                </div>
              </div>
            </form>
          </article>
