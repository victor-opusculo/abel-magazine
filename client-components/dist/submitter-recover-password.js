 // Lego version 1.0.0
  import { h, Component } from './lego.min.js'
   
    import { render } from './lego.min.js';
   
    Component.prototype.render = function(state)
    {
      const childs = Array.from(this.childNodes);
      this.__originalChildren = childs.length && !this.__originalChildren?.length ? childs : this.__originalChildren;

       this.__state.slotId = `slot_${performance.now().toString().replace('.','')}_${Math.floor(Math.random() * 1000)}`;
   
      this.setState(state);
      if(!this.__isConnected) return
   
      const rendered = render([
        this.vdom({ state: this.__state }),
        this.vstyle({ state: this.__state }),
      ], this.document);
   
      const slot = this.document.querySelector(`#${this.__state.slotId}`);
      if (slot)
         for (const c of this.__originalChildren)
             slot.appendChild(c);
            
      return rendered;
    };

  
    const state = 
    {
        email: '',
        mode: 'askEmail',
        otpId: null,
        waiting: false,
        currentOtp: '',
        newPassword: '',
        newPassword2: '',

        lang: {}
    };

    const methods = 
    {
        emailChange(e) { this.render({ ...this.state, email: e.target.value }); },
        otpChange(e) { this.render({ ...this.state, currentOtp: e.target.value }); },
        newPasswordChange(e) { this.render({ ...this.state, newPassword: e.target.value }); },
        newPassword2Change(e) { this.render({ ...this.state, newPassword2: e.target.value }); },

        onSubmit(e)
        {
            e.preventDefault();

            this.render({ ...this.state, waiting: true });

            if (this.state.mode === 'askEmail')
            {
                import(AbelMagazine.functionUrl('/submitter'))
                .then(module => module.createOtp({ email: this.state.email }))
                .then(AbelMagazine.Alerts.pushFromJsonResult)
                .then(([ ret, json]) =>
                {
                    if (json.success && json.otpId)
                        this.render({ ...this.state, otpId: json.otpId, mode: 'changePassword' });
                })
                .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorGeneratingOtp))
                .finally(() => this.render({ ...this.state, waiting: false }));
            }
            else if (this.state.mode === 'changePassword')
            {
                if (this.state.newPassword !== this.state.newPassword2)
                {
                    Parlaflix.Alerts.push(Parlaflix.Alerts.types.error, this.state.lang.forms.passwordsNotEqual);
                    this.render({ ...this.state, waiting: false });
                    return;
                }

                import(AbelMagazine.functionUrl('/submitter'))
                .then(module => module.changePassword({ otpId: this.state.otpId, givenOtp: this.state.currentOtp, newPassword: this.state.newPassword }))
                .then(AbelMagazine.Alerts.pushFromJsonResult)
                .then(([ ret, json]) =>
                {
                    if (json.success)
                        window.location.href = AbelMagazine.Helpers.URLGenerator.generatePageUrl('/submitter/login');
                    else if (json.reset)
                        this.render({ ...this.state, mode: 'askEmail', otpId: null, currentOtp: '', newPassword: '', newPassword2: '' });
                })
                .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorGeneratingOtp))
                .finally(() => this.render({ ...this.state, waiting: false }));
            }
        }
    };

    function setup()
    {
        this.state = { ...this.state, lang: JSON.parse(this.getAttribute('langJson')) };
    }


  const __template = function({ state }) {
    return [  
    h("form", {"class": `mx-auto max-w-[700px]`, "onsubmit": this.onSubmit.bind(this)}, [
      ((state.mode === 'askEmail') ? h("div", {}, [
        h("ext-label", {"label": `${state.lang.forms.email}`}, [
          h("input", {"type": `email`, "class": `w-full`, "maxlength": `140`, "value": state.email, "oninput": this.emailChange.bind(this), "required": ``}, "")
        ])
      ]) : ''),
      ((state.mode === 'changePassword') ? h("div", {}, [
        h("ext-label", {"label": `${state.lang.forms.codeSentToYourEmail}`}, [
          h("input", {"type": `text`, "class": `w-full`, "maxlength": `6`, "value": state.currentOtp, "oninput": this.otpChange.bind(this), "required": ``}, "")
        ]),
        h("ext-label", {"label": `${state.lang.forms.newPassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "value": state.newPassword, "oninput": this.newPasswordChange.bind(this), "required": ``}, "")
        ]),
        h("ext-label", {"label": `${state.lang.forms.retypePassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "value": state.newPassword2, "oninput": this.newPassword2Change.bind(this), "required": ``}, "")
        ])
      ]) : ''),
      h("div", {"class": `text-center my-4`}, [
        h("button", {"type": `submit`, "class": `btn`, "disabled": state.waiting}, `${state.waiting ? state.lang.forms.wait : state.lang.forms.proceed}`)
      ])
    ])
  ]
  }

  const __style = function({ state }) {
    return h('style', {}, `
      
      
    `)
  }

  // -- Lego Core
  export default class Lego extends Component {
    init() {
      this.useShadowDOM = false
      if(typeof state === 'object') this.__state = Object.assign({}, state, this.__state)
      if(typeof methods === 'object') Object.keys(methods).forEach(methodName => this[methodName] = methods[methodName])
      if(typeof connected === 'function') this.connected = connected
      if(typeof setup === 'function') setup.bind(this)()
    }
    get vdom() { return __template }
    get vstyle() { return __style }
  }
  // -- End Lego Core

  
