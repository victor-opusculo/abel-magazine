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
        id: 0,
        waiting: false,
        name: '',
        description: '',
        filename: null,
        files: null,
        lang: {},
    };

    const methods = 
    {
        changeField(e)
        {
            if (e.target.getAttribute('data-fieldname') === 'filename')
            {
                this.render({ ...this.state, files: e.target.files });
            }

            this.render({ ...this.state, [e.target.getAttribute('data-fieldname')]: e.target.value });
        },

        create(formData)
        {
            import(AbelMagazine.functionUrl('/admin/panel/media'))
            .then(module => module.create(formData))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(([ ret, json ]) =>
                {
                    if (json.success && json.newId)
                        window.location.href = AbelMagazine.Helpers.URLGenerator.generatePageUrl('/admin/panel/media/' + json.newId);
                    else
                        this.render({ ...this.state, waiting: false });
                })
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorCreateMedia));
        },

        edit(formData)
        {
            import(AbelMagazine.functionUrl(`/admin/panel/media/${this.state.id}`))
            .then(module => module.edit(formData))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(_ => this.render({ ...this.state, waiting: false }))
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorEditMedia));
        },

        submit(e)
        {
            e.preventDefault();

            const formData = new FormData();

            formData.append('media:name', this.state.name);
            formData.append('media:description', this.state.description);
            formData.append('mediaFile', this.state.files && this.state.files[0] ? this.state.files[0] : null);

            this.render({ ...this.state, waiting: true });

            if (!this.state.id)
                this.create(formData);
            else
                this.edit(formData);
        }
    };

    function setup()
    {
        this.render({ ...this.state, lang: JSON.parse(this.getAttribute('langJson')) });
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.name}`}, [
        h("input", {"type": `text`, "class": `w-full`, "maxlength": `140`, "required": ``, "data-fieldname": `name`, "value": `${state.name}`, "oninput": this.changeField.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.description}`, "linebreak": `1`}, [
        h("textarea", {"class": `w-full`, "row": `6`, "data-fieldname": `description`, "oninput": this.changeField.bind(this)}, `${state.description}`)
      ]),
      h("ext-label", {"label": `${state.lang.forms.file}`}, [
        h("input", {"type": `file`, "class": `file:btn`, "data-fieldname": `filename`, "required": state.id ? false : true, "onchange": this.changeField.bind(this)}, "")
      ]),
      h("div", {"class": `text-center mt-2`}, [
        h("button", {"type": `submit`, "class": `btn`, "disabled": state.waiting}, [
          ((state.waiting) ? h("loading-spinner", {"additionalclasses": `invert w-[1em] h-[1em]`}, "") : ''),
`
                ${state.lang.forms.save}
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

  
