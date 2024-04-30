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
        full_name: '',
        email: '',
        telephone: '',
        subject: '',
        message: '',
        lang: {},
        waiting: false
    };

    const methods =
    {
        changeField(e)
        {
            this.render({ ...this.state, [ e.target.name ]: e.target.value });
        },

        clearForm()
        {
            this.render({ ...this.state, full_name: '', email: '', telephone: '', subject: '', message: '' });
        },

        submit(e)
        {
            e.preventDefault();

            this.render({ ...this.state, waiting: true });

            const { lang, waiting, ...data } = this.state;
            import(AbelMagazine.functionUrl(`/base`))
            .then(module => module.contactEmail(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(([ ret, json ]) => json.success ? this.clearForm() : null) 
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorSendingMessage))
            .finally(() => this.render({ ...this.state, waiting: false }));
        }
    };

    function setup()
    {
        this.state = { ...this.state, lang: JSON.parse(this.getAttribute('langJson')) };
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.fullName}`}, [
        h("input", {"type": `text`, "maxlength": `140`, "required": ``, "class": `w-full`, "name": `full_name`, "value": state.full_name, "onchange": this.changeField.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.email}`}, [
        h("input", {"type": `email`, "maxlength": `140`, "required": ``, "class": `w-full`, "name": `email`, "value": state.email, "onchange": this.changeField.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.telephone}`}, [
        h("input", {"type": `text`, "maxlength": `80`, "class": `w-full`, "name": `telephone`, "value": state.telephone, "onchange": this.changeField.bind(this), "placeholder": `${state.lang.forms.optional}`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.subject}`}, [
        h("input", {"type": `text`, "maxlength": `140`, "required": ``, "class": `w-full`, "name": `subject`, "value": state.subject, "onchange": this.changeField.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.message}`, "linebreak": `1`}, [
        h("textarea", {"name": `message`, "onchange": this.changeField.bind(this), "value": state.message, "required": ``, "rows": `6`, "class": `w-full`}, "")
      ]),
      h("div", {"class": `text-center`}, [
        h("button", {"type": `submit`, "class": `btn`, "disabled": state.waiting}, [
          ((state.waiting) ? h("loading-spinner", {"additionalclasses": `invert w-[1em] h-[1em]`}, "") : ''),
`
                ${state.lang.forms.send}
            `
        ])
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

  
