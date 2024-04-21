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
        article_id: null,
        assessor_name: '',
        assessor_email: '',
        lang: {}
    };

    const methods = 
    {
        changeField(e)
        {
            this.render({ ...this.state, [e.target.name]: e.target.value });
        },

        submit(e)
        {
            e.preventDefault();

            const data = {};
            for (const prop in this.state)
                if (prop !== 'lang')
                    data['assessors_evaluation_tokens:' + prop] = this.state[prop];

            import(AbelMagazine.functionUrl(`/admin/panel/articles/${this.state.article_id}/evaluation_tokens`))
            .then(module => module.create(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(AbelMagazine.Helpers.URLGenerator.goToPageOnSuccess(`/admin/panel/articles/${this.state.article_id}/evaluation_tokens`))
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorCreatingToken));
        }
    };

    function setup()
    {
        this.state = { ...this.state, lang: JSON.parse(this.getAttribute('langJson')) };
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.reviewerName}`}, [
        h("input", {"type": `text`, "class": `w-full`, "onchange": this.changeField.bind(this), "name": `assessor_name`, "value": state.assessor_name, "required": ``}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.reviewerEmail}`}, [
        h("input", {"type": `email`, "class": `w-full`, "onchange": this.changeField.bind(this), "name": `assessor_email`, "value": state.assessor_email, "required": ``}, "")
      ]),
      h("div", {"class": `mt-4 text-center`}, [
        h("button", {"type": `submit`, "class": `btn`}, `${state.lang.forms.generateToken}`)
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

  
